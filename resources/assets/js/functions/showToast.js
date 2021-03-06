export function showToast (text) {
  try {
    android.showToast(text)
  } catch (err) {
    alert(text)
  }
}
