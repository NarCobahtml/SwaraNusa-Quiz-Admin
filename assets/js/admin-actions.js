import { config } from "./firebase-client.js";
import {
  deleteFirestoreDocument,
  deleteSupabaseAssetUrl,
  escapeAttribute,
  escapeHtml,
  numberOf,
  resolveAssetUrl,
  saveFirestoreDocument,
  valueOf,
} from "./admin-utils.js";

const REWARD_STORAGE = {
  badge: {
    bucket: config?.supabase?.rewardBucket || "reward",
    folder: "badges",
  },
  instrumentImage: {
    bucket: config?.supabase?.rewardBucket || "reward",
    folder: "instruments",
  },
};

function quizMediaStorage(mediaType, item) {
  const modeFolder = mediaType === "audio" ? "tebak_suara" : "tebak_gambar";
  return {
    bucket: config?.supabase?.mediaBucket || "quiz-media",
    folder: `${modeFolder}/level_${levelNumberFromQuiz(item)}`,
  };
}

function levelNumberFromQuiz(item) {
  const level = context?.levels?.find((row) => row.id === item.levelId);
  if (level?.levelNumber) return Number(level.levelNumber);

  const match = String(item.levelId || "").match(/(?:^|_)(\d+)$/);
  return Number(match?.[1] || 1);
}

let context = null;
let bound = false;

export function bindAdminActions(nextContext) {
  context = nextContext;
  if (bound) return;
  bound = true;

  document.addEventListener("click", async (event) => {
    const button = event.target.closest("[data-action][data-type][data-id]");
    if (!button) return;

    const item = findItem(button.dataset.type, button.dataset.id);
    if (!item) {
      await window.Swal.fire(
        "Data tidak ditemukan",
        "Muat ulang halaman lalu coba lagi.",
        "error",
      );
      return;
    }

    if (button.dataset.action === "detail") {
      showDetail(button.dataset.type, item);
      return;
    }

    if (button.dataset.action === "edit") {
      await showEdit(button.dataset.type, item);
      return;
    }

    if (button.dataset.action === "delete") {
      await showDelete(button.dataset.type, item);
    }
  });
}

function findItem(type, id) {
  if (type === "quiz") return context?.questions.find((row) => row.id === id);
  if (type === "reward") return context?.rewards.find((row) => row.id === id);
  if (type === "user") return context?.users.find((row) => row.id === id);
  return null;
}

function showDetail(type, item) {
  const rows = detailRows(type, item)
    .map(
      ([label, value]) => `
      <dt class="col-sm-4">${escapeHtml(label)}</dt>
      <dd class="col-sm-8">${escapeHtml(value || "-")}</dd>
    `,
    )
    .join("");

  window.Swal.fire({
    title: detailTitle(type, item),
    html: `<dl class="row text-left mb-0">${rows}</dl>`,
    width: 720,
    confirmButtonText: "Tutup",
  });
}

function detailRows(type, item) {
  if (type === "quiz") {
    return [
      ["ID", item.id],
      ["Mode", item.modeId],
      ["Level", item.levelId],
      ["Nomor Soal", item.questionNumber],
      ["Judul", item.title],
      ["Pertanyaan", item.questionText],
      ["Media Type", item.mediaType],
      ["Media URL", item.mediaUrl],
      ["Jawaban Benar", item.correctAnswer],
      ["Status", item.isActive === true ? "Aktif" : "Nonaktif"],
    ];
  }

  if (type === "reward") {
    return [
      ["ID", item.id],
      ["Tipe", item.type],
      ["Nama", item.name],
      ["Deskripsi", item.description || item.unlockCondition],
      ["Asset URL", item.assetUrl || item.iconUrl || item.imageUrl],
      ["Note URLs", (item.noteUrls || []).join(", ")],
      ["Status", item.isActive === true ? "Aktif" : "Nonaktif"],
    ];
  }

  return [
    ["ID", item.id],
    ["Nama", item.name || item.username],
    ["Email", item.email],
    ["Role", item.role || "user"],
    ["XP", item.xp],
    ["Status", userStatus(item)],
  ];
}

function detailTitle(type, item) {
  if (type === "quiz") return item.title || "Detail Soal";
  if (type === "reward") return item.name || "Detail Hadiah";
  return item.name || item.email || "Detail Pengguna";
}

async function showEdit(type, item) {
  const configByType = {
    quiz: quizEditConfig,
    reward: rewardEditConfig,
    user: userEditConfig,
  }[type](item);

  const result = await window.Swal.fire({
    title: configByType.title,
    html: `<form class="text-left" data-admin-edit-form>${configByType.html}</form>`,
    width: 760,
    showCancelButton: true,
    confirmButtonText: "Simpan",
    cancelButtonText: "Batal",
    focusConfirm: false,
    preConfirm: async () => {
      const form = window.Swal.getPopup().querySelector(
        "[data-admin-edit-form]",
      );
      try {
        await configByType.save(form);
        return true;
      } catch (error) {
        window.Swal.showValidationMessage(error.message);
        return false;
      }
    },
  });

  if (result.isConfirmed) {
    await window.Swal.fire(
      "Tersimpan",
      "Perubahan berhasil disimpan.",
      "success",
    );
    window.location.reload();
  }
}

async function showDelete(type, item) {
  const label = detailTitle(type, item);
  const result = await window.Swal.fire({
    title: "Hapus data?",
    text: `Data "${label}" akan dihapus dari Firestore${type === "user" ? "." : " dan file Supabase terkait akan ikut dihapus jika ada."}`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ya, hapus",
    cancelButtonText: "Batal",
    confirmButtonColor: "#dc3545",
    showLoaderOnConfirm: true,
    preConfirm: async () => {
      try {
        await deleteRelatedSupabaseAssets(type, item);
        await deleteFirestoreDocument(deleteCollection(type, item), item.id);
        if (type === "user") {
          await deleteFirestoreDocument("leaderboards/global/entries", item.id);
        }
        return true;
      } catch (error) {
        window.Swal.showValidationMessage(error.message);
        return false;
      }
    },
  });

  if (result.isConfirmed) {
    await window.Swal.fire("Terhapus", "Data berhasil dihapus.", "success");
    window.location.reload();
  }
}

async function deleteRelatedSupabaseAssets(type, item) {
  if (type === "quiz") {
    await deleteSupabaseAssetUrl(context.supabase, item.mediaUrl);
    return;
  }

  if (type === "reward") {
    await Promise.all([
      deleteSupabaseAssetUrl(context.supabase, item.assetUrl || item.iconUrl || item.imageUrl),
      deleteSupabaseAssetUrl(context.supabase, item.audioUrl),
    ]);
  }
}

function deleteCollection(type, item) {
  if (type === "quiz") return "questions";
  if (type === "reward") return item.collection || (item.type === "badge" ? "achievements" : "instruments");
  if (type === "user") return "users";
  throw new Error("Tipe data tidak dikenali.");
}

function quizEditConfig(item) {
  return {
    title: "Edit Soal",
    html: `
      ${input("Judul", "quiz-title", item.title)}
      ${textarea("Pertanyaan", "quiz-question-text", item.questionText)}
      <div class="form-group">
        <label>Media Type</label>
        <select class="form-control" data-quiz-media-type>
          ${option("image", item.mediaType)}
          ${option("audio", item.mediaType)}
          ${option("none", item.mediaType)}
        </select>
      </div>
      ${input("Media URL", "quiz-media-url", item.mediaUrl)}
      ${fileInput("Upload ulang media", "quiz-media-file", "image/*,audio/*")}
      ${textarea("Opsi Jawaban", "quiz-options", (item.options || []).join("\n"))}
      ${input("Jawaban Benar", "quiz-correct-answer", item.correctAnswer)}
      ${textarea("Penjelasan", "quiz-explanation", item.explanation)}
      ${numberInput("Waktu (detik)", "quiz-time-limit", item.timeLimitSeconds)}
      ${numberInput("Poin", "quiz-points", item.points)}
      ${checkbox("Aktif", "quiz-active", item.isActive === true)}
    `,
    save: async (form) => {
      const mediaType = valueOf(form, "[data-quiz-media-type]") || "none";
      const file = form.querySelector("[data-quiz-media-file]").files[0];
      const options = valueOf(form, "[data-quiz-options]")
        .split("\n")
        .map((value) => value.trim())
        .filter(Boolean);

      if (!valueOf(form, "[data-quiz-question-text]"))
        throw new Error("Pertanyaan wajib diisi.");
      if (options.length !== 4)
        throw new Error("Opsi jawaban harus tepat 4 baris.");

      const mediaUrl =
        mediaType === "none"
          ? ""
          : await resolveAssetUrl(
              context.supabase,
              file,
              valueOf(form, "[data-quiz-media-url]"),
              quizMediaStorage(mediaType, item),
            );

      await saveFirestoreDocument(
        "questions",
        item.id,
        {
          modeId: item.modeId || "",
          levelId: item.levelId || "",
          questionNumber: Number(item.questionNumber || 0),
          title: valueOf(form, "[data-quiz-title]"),
          questionText: valueOf(form, "[data-quiz-question-text]"),
          mediaType,
          mediaUrl,
          options,
          correctAnswer: valueOf(form, "[data-quiz-correct-answer]"),
          explanation: valueOf(form, "[data-quiz-explanation]"),
          timeLimitSeconds: numberOf(form, "[data-quiz-time-limit]"),
          points: numberOf(form, "[data-quiz-points]"),
          isActive: form.querySelector("[data-quiz-active]").checked,
        },
        { preserveCreatedAt: true },
      );

      if (item.mediaUrl && item.mediaUrl !== mediaUrl) {
        await deleteSupabaseAssetUrl(context.supabase, item.mediaUrl);
      }
    },
  };
}

function rewardEditConfig(item) {
  const isBadge = item.collection === "achievements" || item.type === "badge";
  return {
    title: isBadge ? "Edit Badge" : "Edit Instrumen",
    html: isBadge
      ? `
      ${input("Nama", "reward-name", item.name)}
      ${textarea("Deskripsi", "reward-description", item.description)}
      ${input("Icon URL", "reward-asset-url", item.iconUrl || item.assetUrl)}
      ${fileInput("Upload ulang badge", "reward-asset-file", "image/*")}
      ${input("Condition Type", "reward-condition-type", item.conditionType)}
      ${numberInput("Condition Value", "reward-condition-value", item.conditionValue)}
      ${numberInput("Stars", "reward-stars", item.stars || 1)}
      ${checkbox("Aktif", "reward-active", item.isActive === true)}
    `
      : `
      ${input("Nama", "reward-name", item.name)}
      ${input("Daerah", "reward-region", item.region)}
      ${textarea("Deskripsi", "reward-description", item.description)}
      ${input("Image URL", "reward-asset-url", item.imageUrl || item.assetUrl)}
      ${fileInput("Upload ulang gambar instrumen", "reward-asset-file", "image/*")}
      ${textarea("Note URLs / Audio Minigame", "reward-note-urls", (item.noteUrls || []).join("\n"))}
      ${numberInput("Urutan", "reward-sort-order", item.sortOrder)}
      ${numberInput("Harga Koin", "reward-price", item.price)}
      ${checkbox("Buka minigame saat instrumen diklik", "reward-opens-minigame", item.opensMinigame === true)}
      ${checkbox("Aktif", "reward-active", item.isActive === true)}
    `,
    save: async (form) => {
      if (!valueOf(form, "[data-reward-name]"))
        throw new Error("Nama wajib diisi.");

      if (isBadge) {
        const file = form.querySelector("[data-reward-asset-file]").files[0];
        const oldIconUrl = item.iconUrl || item.assetUrl;
        const iconUrl = await resolveAssetUrl(
          context.supabase,
          file,
          valueOf(form, "[data-reward-asset-url]"),
          REWARD_STORAGE.badge,
        );

        await saveFirestoreDocument(
          "achievements",
          item.id,
          {
            name: valueOf(form, "[data-reward-name]"),
            description: valueOf(form, "[data-reward-description]"),
            iconUrl,
            stars: numberOf(form, "[data-reward-stars]"),
            conditionType: valueOf(form, "[data-reward-condition-type]"),
            conditionValue: numberOf(form, "[data-reward-condition-value]"),
            isActive: form.querySelector("[data-reward-active]").checked,
          },
          { preserveCreatedAt: true },
        );

        if (oldIconUrl && oldIconUrl !== iconUrl) {
          await deleteSupabaseAssetUrl(context.supabase, oldIconUrl);
        }
        return;
      }

      const file = form.querySelector("[data-reward-asset-file]").files[0];
      const oldImageUrl = item.imageUrl || item.assetUrl;
      const imageUrl = await resolveAssetUrl(
        context.supabase,
        file,
        valueOf(form, "[data-reward-asset-url]"),
        REWARD_STORAGE.instrumentImage,
      );

      await saveFirestoreDocument(
        "instruments",
        item.id,
        {
          name: valueOf(form, "[data-reward-name]"),
          region: valueOf(form, "[data-reward-region]"),
          description: valueOf(form, "[data-reward-description]"),
          imageUrl,
          noteUrls: linesOf(valueOf(form, "[data-reward-note-urls]")),
          sortOrder: numberOf(form, "[data-reward-sort-order]"),
          price: numberOf(form, "[data-reward-price]"),
          opensMinigame: form.querySelector("[data-reward-opens-minigame]").checked,
          isActive: form.querySelector("[data-reward-active]").checked,
        },
        { preserveCreatedAt: true },
      );

      if (oldImageUrl && oldImageUrl !== imageUrl) {
        await deleteSupabaseAssetUrl(context.supabase, oldImageUrl);
      }
    },
  };
}

function userEditConfig(item) {
  return {
    title: "Edit Pengguna",
    html: `
      ${input("Nama", "user-name", item.name || item.username)}
      ${input("Email", "user-email", item.email, "email")}
      ${input("Role", "user-role", item.role || "user")}
      ${numberInput("XP", "user-xp", item.xp)}
      <div class="form-group">
        <label>Status</label>
        <select class="form-control" data-user-status>
          ${option("aktif", userStatus(item))}
          ${option("nonaktif", userStatus(item))}
        </select>
      </div>
    `,
    save: async (form) => {
      if (!valueOf(form, "[data-user-name]"))
        throw new Error("Nama wajib diisi.");

      await saveFirestoreDocument(
        "users",
        item.id,
        {
          name: valueOf(form, "[data-user-name]"),
          email: valueOf(form, "[data-user-email]"),
          role: valueOf(form, "[data-user-role]") || "user",
          xp: numberOf(form, "[data-user-xp]"),
          status: valueOf(form, "[data-user-status]"),
          isActive: valueOf(form, "[data-user-status]") === "aktif",
        },
        { preserveCreatedAt: true },
      );
      await saveFirestoreDocument(
        "leaderboards/global/entries",
        item.id,
        {
          periodType: "global",
          periodKey: "global",
          uid: item.id,
          name: valueOf(form, "[data-user-name]"),
          username: item.username || "",
          avatarUrl: item.avatarUrl || "",
          level: Number(item.level || 1),
          xp: numberOf(form, "[data-user-xp]"),
          score: numberOf(form, "[data-user-xp]"),
          quizCompleted: Number(item.quizCompleted || 0),
        },
        { preserveCreatedAt: true },
      );
    },
  };
}

function input(label, name, value = "", type = "text") {
  return `
    <div class="form-group">
      <label>${escapeHtml(label)}</label>
      <input type="${type}" class="form-control" data-${name} value="${escapeAttribute(value || "")}">
    </div>
  `;
}

function numberInput(label, name, value = 0) {
  return input(label, name, Number(value || 0), "number");
}

function textarea(label, name, value = "") {
  return `
    <div class="form-group">
      <label>${escapeHtml(label)}</label>
      <textarea class="form-control" rows="3" data-${name}>${escapeHtml(value || "")}</textarea>
    </div>
  `;
}

function fileInput(label, name, accept) {
  return `
    <div class="form-group">
      <label>${escapeHtml(label)}</label>
      <input type="file" class="form-control-file" data-${name} accept="${escapeAttribute(accept)}">
    </div>
  `;
}

function checkbox(label, name, checked) {
  return `
    <div class="form-check mb-3">
      <input type="checkbox" class="form-check-input" id="${name}" data-${name} ${checked ? "checked" : ""}>
      <label class="form-check-label" for="${name}">${escapeHtml(label)}</label>
    </div>
  `;
}

function option(value, selectedValue) {
  return `<option value="${escapeAttribute(value)}" ${String(value) === String(selectedValue || "") ? "selected" : ""}>${escapeHtml(value)}</option>`;
}

function userStatus(item) {
  if (item.status) return item.status;
  return item.isActive === false ? "nonaktif" : "aktif";
}

function linesOf(value) {
  return String(value || "")
    .split("\n")
    .map((item) => item.trim())
    .filter(Boolean);
}
