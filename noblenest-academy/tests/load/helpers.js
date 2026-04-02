/**
 * Artillery helpers.js
 * - pickRandomCourse: picks a slug from a pre-seeded list
 * - randomChild:      picks a child ID for authenticated flows
 * - setRandomLang:    rotates between the 8 supported locales
 */

'use strict';

// Known slugs seeded in the database (update to match your seeders)
const COURSE_SLUGS = [
  'arabic-for-beginners',
  'early-maths-seedling',
  'french-discovery',
  'english-phonics-sapling',
  'stem-for-kids-grove',
  'mandarin-basics',
  'quran-recitation-foundations',
  'coding-scratch-grove',
];

const CHILD_IDS = [1, 2, 3, 4, 5];   // adjust to match your test DB seed
const LANGUAGES  = ['en', 'fr', 'ar', 'zh', 'ur', 'ru', 'es', 'ko'];

/**
 * Sets `context.vars.courseSlug` to a random slug.
 */
function pickRandomCourse(context, events, done) {
  context.vars.courseSlug = COURSE_SLUGS[
    Math.floor(Math.random() * COURSE_SLUGS.length)
  ];
  return done();
}

/**
 * Sets `context.vars.childId` to a random child ID.
 */
function randomChild(context, events, done) {
  context.vars.childId = CHILD_IDS[
    Math.floor(Math.random() * CHILD_IDS.length)
  ];
  return done();
}

/**
 * Sets `context.vars.lang` to a random language code.
 */
function setRandomLang(context, events, done) {
  context.vars.lang = LANGUAGES[
    Math.floor(Math.random() * LANGUAGES.length)
  ];
  return done();
}

module.exports = { pickRandomCourse, randomChild, setRandomLang };
