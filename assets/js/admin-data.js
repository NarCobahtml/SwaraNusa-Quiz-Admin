import {
  buildRewards,
  fetchCollection,
  withoutTemplates,
} from './admin-utils.js';

export async function loadBackendData(db, supabase, paths, requestedCollections = null) {
  const requested = new Set(requestedCollections || [
    'users',
    'modes',
    'levels',
    'questions',
    'instruments',
    'achievements',
  ]);

  const [
    users,
    modes,
    levels,
    questions,
    instruments,
    achievements,
  ] = await Promise.all([
    requested.has('users') ? fetchCollection(db, paths.users) : [],
    requested.has('modes') ? fetchCollection(db, paths.quizModes) : [],
    requested.has('levels') ? fetchCollection(db, paths.levels) : [],
    requested.has('questions') ? fetchCollection(db, paths.questions) : [],
    requested.has('instruments') ? fetchCollection(db, paths.instruments) : [],
    requested.has('achievements') ? fetchCollection(db, paths.achievements) : [],
  ]);

  return {
    supabase,
    users: sortBy(withoutTemplates(users), 'xp', 'desc'),
    modes: sortBy(withoutTemplates(modes), 'order'),
    levels: sortBy(withoutTemplates(levels), 'levelNumber'),
    questions: sortBy(withoutTemplates(questions), 'questionNumber'),
    rewards: buildRewards(
      sortBy(withoutTemplates(instruments), 'name'),
      sortBy(withoutTemplates(achievements), 'name'),
    ),
  };
}

function sortBy(rows, field, direction = 'asc') {
  const multiplier = direction === 'desc' ? -1 : 1;
  return [...rows].sort((a, b) => {
    const left = a[field] ?? '';
    const right = b[field] ?? '';
    if (typeof left === 'number' && typeof right === 'number') {
      return (left - right) * multiplier;
    }
    return String(left).localeCompare(String(right), 'id') * multiplier;
  });
}
