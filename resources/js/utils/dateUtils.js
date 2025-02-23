export const formatDateString = (value) => {
  let cleaned = value.replace(/\D/g, '');

  if (cleaned.length > 0) {
    if (cleaned.length > 4) {
      cleaned = cleaned.slice(0, 4) + '-' + cleaned.slice(4);
    }
    if (cleaned.length > 7) {
      cleaned = cleaned.slice(0, 7) + '-' + cleaned.slice(7);
    }
    cleaned = cleaned.slice(0, 10);
  }

  return cleaned;
};

export const isValidDateFormat = (dateString) => {
  const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
  return dateRegex.test(dateString);
};
