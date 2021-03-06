export function formatQuotation (value) {
  let number = value,
    currencyDecimals = 2,
    decimalSign = ',',
    thousandSign = '.',
    signal = number < 0 ? '-' : '',
    i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(currencyDecimals))),
    j = (j = i.length) > 3 ? j % 3 : 0

  return signal + (j ? i.substr(0, j) + thousandSign : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousandSign) + (currencyDecimals ? decimalSign + Math.abs(number - i).toFixed(currencyDecimals).slice(2) : '')
}