/**
 * Reusable date range helpers for filters (week range & month range).
 */

/**
 * Format a Date as "YYYY-MM-DD" (local, no timezone shift).
 * @param {Date} date
 * @returns {string}
 */
export const toDateStr = (date) => {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const d = String(date.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
};

/**
 * Format a Date as "dd/mm" for compact display (e.g. table headers).
 * @param {Date} date
 * @returns {string}
 */
export const formatShortDate = (date) =>
    date.toLocaleDateString("id-ID", { day: "2-digit", month: "2-digit" });

/**
 * Get the Monday of the week containing the given date.
 * @param {Date} date
 * @returns {Date}
 */
export const mondayOfWeek = (date) => {
    const d = new Date(date);
    const day = d.getDay();
    const diff = day === 0 ? -6 : 1 - day;
    d.setDate(d.getDate() + diff);
    return d;
};

/**
 * Get the Monday of the current week (local time).
 * @returns {Date}
 */
export const currentMonday = () => mondayOfWeek(new Date());

/**
 * Get the Monday of the week `offsetWeeks` away from the current week.
 * @param {number} offsetWeeks
 * @returns {Date}
 */
export const startOfWeek = (offsetWeeks = 0) => {
    const monday = currentMonday();
    monday.setDate(monday.getDate() + offsetWeeks * 7);
    return monday;
};

/**
 * Get the Sunday of the week `offsetWeeks` away from the current week.
 * @param {number} offsetWeeks
 * @returns {Date}
 */
export const endOfWeek = (offsetWeeks = 0) => {
    const sunday = startOfWeek(offsetWeeks);
    sunday.setDate(sunday.getDate() + 6);
    return sunday;
};

/**
 * Get a [start, end] date range spanning `spanWeeks` week(s) starting from the current week.
 * e.g. getDefaultWeekRange(1) -> this week only
 *      getDefaultWeekRange(2) -> this week + next week
 *      getDefaultWeekRange(3) -> this week + next 2 weeks
 * @param {number} spanWeeks
 * @returns {[Date, Date]}
 */
export const getDefaultWeekRange = (spanWeeks = 1) => {
    return [startOfWeek(0), endOfWeek(spanWeeks - 1)];
};

/**
 * Get the first day of the month `offset` months away from the current month.
 * @param {number} offset
 * @returns {Date}
 */
export const startOfMonth = (offset = 0) => {
    const now = new Date();
    return new Date(now.getFullYear(), now.getMonth() + offset, 1);
};

/**
 * Get the last day of the month `offset` months away from the current month.
 * @param {number} offset
 * @returns {Date}
 */
export const endOfMonth = (offset = 0) => {
    const now = new Date();
    return new Date(now.getFullYear(), now.getMonth() + offset + 1, 0);
};

/**
 * Get a [start, end] date range spanning `spanMonths` month(s) starting from the current month.
 * e.g. getDefaultMonthRange(1) -> this month only
 *      getDefaultMonthRange(2) -> this month + next month
 *      getDefaultMonthRange(3) -> this month + next 2 months
 * @param {number} spanMonths
 * @returns {[Date, Date]}
 */
export const getDefaultMonthRange = (spanMonths = 1) => {
    return [startOfMonth(), endOfMonth(spanMonths - 1)];
};

/**
 * Get a [start, end] date range spanning `spanMonths` month(s) going backward,
 * ending at the current month.
 * e.g. getPastMonthRange(1) -> this month only
 *      getPastMonthRange(6) -> 5 months ago until this month (6 months total)
 * @param {number} spanMonths
 * @returns {[Date, Date]}
 */
export const getPastMonthRange = (spanMonths = 1) => {
    return [startOfMonth(-(spanMonths - 1)), endOfMonth(0)];
};

/**
 * Get a [start, end] date range centered on the current week, going back
 * `weeksBefore` week(s) and forward `weeksAfter` week(s).
 * e.g. getCenteredWeekRange(1, 1) -> last week through next week (3 weeks total)
 *      getCenteredWeekRange(2, 0) -> 2 weeks ago through this week
 * @param {number} weeksBefore
 * @param {number} weeksAfter
 * @returns {[Date, Date]}
 */
export const getCenteredWeekRange = (weeksBefore = 1, weeksAfter = 1) => {
    return [startOfWeek(-weeksBefore), endOfWeek(weeksAfter)];
};
