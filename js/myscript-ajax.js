$(document).ready(function(){
	$("#error").hide();

	$("#myform").submit(function(eventObject){
		eventObject.preventDefault();
	});

	$( "#datepicker" ).datepicker();
	$( "#datepicker" ).change(function(){
	
		$("#error").hide();
		$("#table10").remove();
		$("#result").addClass('loading');
		
		$.ajax({
			type: "GET",			
			cache: false,
			url: 'json.php',
			data: 'date=' +  encodeURIComponent(this.value)
		}).done(function(data){
				/* console.log(data); */
				if (data.length < 2){
					$("#result").removeClass('loading');
					$("#error").fadeIn(750);
				}
				else{
					data = jQuery.parseJSON(data);
					var table = '<table id="table10" width="550" border="0" align="center" cellpadding="5" cellspacing="2">';
						table+= '	<tr>';
						table+= '		<th width="20">&nbsp;</th>';
						table+= '		<th>Фильм</th>';
						table+= '		<th class="left15" width="150">Рейтинг</th>';
						table+= '	</tr>';
					$.each(data, function(index, data){
						table+= '	<tr>';
						table+= '		<td class="number">'+data.position+'.</td>';
						table+= '		<td class="movie" id="table10">'+data.name+' ('+data.year+')</td>';
						table+= '		<td class="rating">'+data.rating+' <span class="votes">('+data.votes+')</span></td>';
						table+= '	</tr>';
					});
						table+= '</table>';
					$("#result").removeClass('loading');
					$("#result").append(table).hide().fadeIn(500);
				}
			});
		});

/* Russian (UTF-8) initialisation for the jQuery UI date picker plugin. */
jQuery(function($){
	$.datepicker.regional['ru'] = {
		closeText: 'Закрыть',
		prevText: '&#x3C;Пред',
		nextText: 'След&#x3E;',
		currentText: 'Сегодня',
		monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
		'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
		monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
		'Июл','Авг','Сен','Окт','Ноя','Дек'],
		dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
		dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
		dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
		weekHeader: 'Нед',
		dateFormat: 'yy-mm-dd',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['ru']);
});

});//End ready