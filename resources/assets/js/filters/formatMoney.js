export function formatMoney (value) {
  let number = value
  let currencySign = 'R$ '
  let currencyDecimals = 2
  let decimalSign = ','
  let thousandSign = '.'
  let signal = number < 0 ? '-' : ''
  let i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(currencyDecimals)))
  let j = (j = i.length) > 3 ? j % 3 : 0

  return currencySign + signal + (j ? i.substr(0, j) + thousandSign : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousandSign) + (currencyDecimals ? decimalSign + Math.abs(number - i).toFixed(currencyDecimals).slice(2) : '')
}
