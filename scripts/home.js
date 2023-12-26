function enablebtn() {
  if (document.getElementById('project').value != '0') {
    document.getElementById('openStructural').disabled = false;
  }
}

$(document).ready(function () {
  $('#openStructural').click(function (e) {
    e.preventDefault();
    $('#structuralModelForm').submit();
  });
});
