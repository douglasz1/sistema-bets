export const logoutAfterInactivity = function () {
  let timeToLogout
  window.onload = resetTimer
  // DOM Events
  document.onmousemove = resetTimer
  document.onkeypress = resetTimer
  document.onmousedown = resetTimer // touchscreen presses
  document.ontouchstart = resetTimer
  document.onclick = resetTimer // touchpad clicks
  document.onscroll = resetTimer // scrolling with arrow keys

  function logout () {
    let xhttp = new XMLHttpRequest()
    xhttp.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
        document.location.reload()
      }
    }
    xhttp.open('GET', '/logout', true)
    xhttp.send()
  }

  function resetTimer () {
    clearTimeout(timeToLogout)
    timeToLogout = setTimeout(logout, 3600000)
    // 1000 milisec = 1 sec
  }
}
