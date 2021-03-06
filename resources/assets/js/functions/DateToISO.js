Date.prototype.toISOString = function () {
  let tzo = -this.getTimezoneOffset()
  let dif = tzo >= 0 ? '+' : '-'
  let pad = function (num) {
    let norm = Math.abs(Math.floor(num))
    return (norm < 10 ? '0' : '') + norm
  }
  return this.getFullYear() +
    '-' + pad(this.getMonth() + 1) +
    '-' + pad(this.getDate()) +
    'T' + pad(this.getHours()) +
    ':' + pad(this.getMinutes()) +
    ':' + pad(this.getSeconds()) +
    dif + pad(tzo / 60) +
    ':' + pad(tzo % 60)
}
