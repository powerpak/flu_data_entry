$(->
  
  $('.table-hover tr').hover(-> $(this).addClass('pointer'), 
  ->$(this).removeClass('pointer'))
  
  $('.table-hover tr').click(->
    $a = $(this).find('a')
    if $a.length then window.location.href = $(this).find('a').attr('href')
  )
  
)