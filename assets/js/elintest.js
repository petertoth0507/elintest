$('document').ready(function () {
  $('#btn-jstree').on('click', function () {
    console.log('Button clicked')
    createJstreeData()
  })
})

function createJstreeData () {
  $('#btn-jstree').prop('disabled', true)
  $.ajax({
    url: load_jstree_url, // Make sure the URL is correct
    type: 'POST',
    dataType: 'json',
    beforeSend: function () {
      $('#modalLoader').modal({ keyboard: false, backdrop: true })
      $('#modalLoader').modal('show')
    },
    success: function (result) {
      try {
        let parsedData = JSON.parse(result) // Check if it's a valid JSON
        loadJstreeData(parsedData)
      } catch (e) {
        console.error('Invalid JSON:', e)
      }

      $('#btn-jstree').removeClass('disabled')
      $('#modalLoader').modal('hide')
    },
    error: function (xhr, status, error) {
      console.log('Error:', error)
      $('#btn-jstree').prop('disabled', false)
      $('#modalLoader').modal('hide')
    },
    complete: function () {
      console.log('AJAX request complete')
    }
  })
}

function loadJstreeData (json) {
  console.log('Loading jstree with data:', json)

  // Ensure the #jstree-div is empty before initializing the tree
  $('#jstree-div').jstree('destroy').empty()

  // Initialize jstree with the data
  $('#jstree-div').jstree({
    core: {
      data: json
    }
  })
}
