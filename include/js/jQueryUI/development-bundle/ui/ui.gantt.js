/*
 * jQuery UI Gantt 1.7.2
 *
 * Copyright (c) 2009 AUTHORS.txt (http://jqueryui.com/about)
 * Copyright (c) 2010 Stéphane Benoist
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * 
 *
 * Depends:
 *	 ui.core.js
 */
(function($) {

$.widget("ui.gantt", {

	_init: function() {

		this.element
			.addClass("ui-gantt"
				+ " ui-widget");

		this.table = $('<table class="ui-gantt-table" cellspacing=0></table>').appendTo(this.element);

		if($.datepicker.regional[this.options.language] != undefined)
		{
			this.monthNames = $.datepicker.regional[this.options.language]['monthNames'];
			this.monthNamesShort = $.datepicker.regional[this.options.language]['monthNamesShort'];
		}
		else
		{
			this.monthNames = ['January','February','March','April','May','June',
			'July','August','September','October','November','December'];
			this.monthNamesShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		}

		$('<thead><tr></tr><tr></tr></thead><tfoot><tr></tr><tr></tr></tfoot>').appendTo(this.table);
		this.titles(this.options.titles);
		this._getTHeadAndTFootCalendar();

		$('<tbody></tbody>').appendTo(this.table);

		this._getTBody();

	},

	destroy: function() {

		this.element
			.removeClass("ui-gantt"
				+ " ui-widget")
			.removeData("gantt");

		this.table.remove();

		$.widget.prototype.destroy.apply(this, arguments);

	},

	titles: function(newTitles) {
		if (newTitles === undefined) {
			return this._titles();
		}

		while(newTitles.length < 3)
		{
			newTitles.push('Title');
		}

		while(newTitles.length > 3)
		{
			newTitles.pop();
		}

		for(var i = 1; i <= 3; i++)
		{
			if(this.table.children('thead').children('tr:first').children('th:nth-child(' + i + ')').html() == null)
			{
				this.table.children('thead').children('tr:first').append('<th rowspan=2 class="ui-widget-header ui-gantt-header">' + newTitles[i-1] + '</th>');
				this.table.children('tfoot').children('tr:first').append('<th rowspan=2 class="ui-widget-header ui-gantt-header">' + newTitles[i-1] + '</th>');
			}
			else
			{
				this.table.children('thead').children('tr:first').children('th:nth-child(' + i + ')').replaceWith('<th rowspan=2 class="ui-widget-header ui-gantt-header">' + newTitles[i-1] + '</th>');
				this.table.children('tfoot').children('tr:first').children('th:nth-child(' + i + ')').replaceWith('<th class="ui-widget-header ui-gantt-header">' + newTitles[i-1] + '</th>');
			}
		}

		this.options.titles = newTitles;

		return this;
	},

	_setData: function(key, value) {

		switch (key) {
			case 'titles':
				this.titles(value);
				this._trigger('change', null, {});
				break;
			case 'tasks':
				this.options.tasks = value;
				this._trigger('change', null, {});
				break;
			case 'displayStartDate':
				this.options.displayStartDate = value;
				this._trigger('change', null, {});
				break;
			case 'displayFinishDate':
				this.options.displayFinishDate = value;
				this._trigger('change', null, {});
				break;
		}

		$.widget.prototype._setData.apply(this, arguments);

	},

	_titles: function() {

		return this.options.titles;

	},

	_tasks: function() {

		return this.options.tasks;

	},

	_displayStartDate: function() {

		return this.options.displayStartDate;

	},

	_displayFinishDate: function() {

		return this.options.displayFinishDate;

	},

	_getTHeadAndTFootCalendar: function() {

		var startDate = this.options.displayStartDate.split('-');
		var startYear = parseInt(startDate[0], 10);
		var startMonth = parseInt(startDate[1], 10) - 1;
		var startDay = parseInt(startDate[2], 10);

		var finishDate = this.options.displayFinishDate.split('-');
		var finishYear = parseInt(finishDate[0], 10);
		var finishMonth = parseInt(finishDate[1], 10) - 1;
		var finishDay = parseInt(finishDate[2], 10);

		var currentMonth = - 1;
		var currentYear = startYear;
		var monthFirstDay = startDay;
		var monthLastDay = 0;
		var theadChildIndex = this.options.titles.length + 1;
		var dayNumber = startDay;

		while(this.table.children('thead').children('tr:first').children('th:nth-child(' + theadChildIndex + ')').html() != null
			&& this.table.children('tfoot').children('tr:first').children('th:nth-child(' + theadChildIndex + ')').html() != null)
		{
			this.table.children('thead').children('tr:first').children('th:nth-child(' + theadChildIndex + ')').remove();
			this.table.children('tfoot').children('tr:first').children('th:nth-child(' + theadChildIndex + ')').remove();
		}
		this.table.children('thead').children('tr:nth-child(2)').html('');
		this.table.children('tfoot').children('tr:nth-child(2)').html('');

		while(new Date(startYear, startMonth, startDay).getFullYear() != new Date(finishYear, finishMonth, finishDay - (-1)).getFullYear()
			|| new Date(startYear, startMonth, startDay).getMonth() != new Date(finishYear, finishMonth, finishDay - (-1)).getMonth()
			|| new Date(startYear, startMonth, startDay).getDate() != new Date(finishYear, finishMonth, finishDay - (-1)).getDate())
		{
			if(currentMonth != parseInt(new Date(startYear, startMonth, startDay).getMonth(), 10))
			{
				currentMonth = parseInt(new Date(startYear, startMonth, startDay).getMonth(), 10);
				currentYear = parseInt(new Date(startYear, startMonth, startDay).getFullYear(), 10);
				var monthNumber = currentMonth + 1;
				if(currentMonth == finishMonth && currentYear == finishYear)
				{
					monthLastDay = finishDay;
				}
				else
				{
					monthLastDay = parseInt(new Date(currentYear, monthNumber, 0).getDate(), 10);
				}
				if(currentMonth != startMonth || currentYear != startYear)
				{
					monthFirstDay = 1;
					dayNumber = 1;
				}

				var monthDuration = monthLastDay - monthFirstDay + 1;
				if(monthDuration < 5)
				{
					var monthName = this.monthNamesShort[monthNumber - 1];
				}
				else
				{
					var monthName = this.monthNames[monthNumber - 1];
				}

				this.table.children('thead').children('tr:first').append('<th colspan=' + monthDuration + ' class="ui-widget-header ui-gantt-header">' + monthName + ' ' + currentYear + '</th>');
				this.table.children('tfoot').children('tr:nth-child(2)').append('<th colspan=' + monthDuration + ' class="ui-widget-header ui-gantt-header">' + monthName + ' ' + currentYear + '</th>');
			}

			if(dayNumber < 10)
			{
				dayNumber = '0' + dayNumber;
			}
			this.table.children('thead').children('tr:nth-child(2)').append('<th class="ui-widget-header ui-gantt-header">' + dayNumber + '</th>');
			this.table.children('tfoot').children('tr:first').append('<th class="ui-widget-header ui-gantt-header">' + dayNumber + '</th>');

			switch(parseInt(new Date(startYear, startMonth, startDay).getDay(), 10))
			{
				case 0 :
					this.table.children('tfoot').children('tr:first').children('th:last').addClass('ui-gantt-sunday');
					this.table.children('thead').children('tr:nth-child(2)').children('th:last').addClass('ui-gantt-sunday');
					break;
				case 1 :
					this.table.children('tfoot').children('tr:first').children('th:last').addClass('ui-gantt-monday');
					this.table.children('thead').children('tr:nth-child(2)').children('th:last').addClass('ui-gantt-monday');
					break;
				case 2 :
					this.table.children('tfoot').children('tr:first').children('th:last').addClass('ui-gantt-tuesday');
					this.table.children('thead').children('tr:nth-child(2)').children('th:last').addClass('ui-gantt-tuesday');
					break;
				case 3 :
					this.table.children('tfoot').children('tr:first').children('th:last').addClass('ui-gantt-wednesday');
					this.table.children('thead').children('tr:nth-child(2)').children('th:last').addClass('ui-gantt-wednesday');
					break;
				case 4 :
					this.table.children('tfoot').children('tr:first').children('th:last').addClass('ui-gantt-thursday');
					this.table.children('thead').children('tr:nth-child(2)').children('th:last').addClass('ui-gantt-thursday');
					break;
				case 5 :
					this.table.children('tfoot').children('tr:first').children('th:last').addClass('ui-gantt-friday');
					this.table.children('thead').children('tr:nth-child(2)').children('th:last').addClass('ui-gantt-friday');
					break;
				case 6 :
					this.table.children('tfoot').children('tr:first').children('th:last').addClass('ui-gantt-saturday');
					this.table.children('thead').children('tr:nth-child(2)').children('th:last').addClass('ui-gantt-saturday');
					break;
			}

			startDay = startDay + 1;
			dayNumber = parseInt(dayNumber, 10) + 1;
		}

	},

	_getTBody: function() {

		this.table.children('tbody').html('');

		for(var i = 0; i < this.options.tasks.length; i++)
		{
			this.addTask(this.options.tasks[i]);
		}

	},

	addTask: function(task) {

		this.table.children('tbody').append('<tr><td>' + task.id + '</td><td>' + task.task + '</td><td>' + task.progression + '</td></tr>');


		if(this._dayBetween(task.startDate, this.options.displayFinishDate) < 0)
		{
			var dayBeforeTaskFinishDate = this.options.displayFinishDate;
		}
		else
		{
			var dayBeforeTaskFinishDate = task.startDate;
		}
		var dayBeforeTask = this._dayBetween(this.options.displayStartDate, dayBeforeTaskFinishDate);


		if(this._dayBetween(this.options.displayStartDate, task.finishDate) < 0)
		{
			var dayAfterTaskStartDate = this.options.displayStartDate;
		}
		else
		{
			var dayAfterTaskStartDate = task.finishDate;
		}
		var dayAfterTask = this._dayBetween(dayAfterTaskStartDate, this.options.displayFinishDate);


		if(dayBeforeTask < 0)
		{
			var startDate = this.options.displayStartDate;
		}
		else
		{
			var startDate = task.startDate;
		}
		if(dayAfterTask < 0)
		{
			var finishDate = this.options.displayFinishDate;
		}
		else
		{
			var finishDate = task.finishDate;
		}
		var dayDuringTask = this._dayBetween(startDate, finishDate) + 1;
		var dayCompleted = Math.round(task.progression/100 * (this._dayBetween(task.startDate, task.finishDate) + 1)); 
		var dayCompletedDuring = Math.min(Math.max(dayCompleted + Math.min(dayBeforeTask, 0),0), dayDuringTask); 


		for(var i = 0; i < dayBeforeTask; i++)
		{
			this.table.children('tbody').children('tr:last').append('<td class="ui-gantt-day"></td>');
		}
		for(var i = 0; i < dayCompletedDuring; i++)
		{
			this.table.children('tbody').children('tr:last').append('<td class="ui-gantt-day"><div class="ui-gantt-task-barr ui-gantt-task-barr-complete"></div></td>');
		}
		for(var i = dayCompletedDuring; i < dayDuringTask; i++)
		{
			this.table.children('tbody').children('tr:last').append('<td class="ui-gantt-day"><div class="ui-gantt-task-barr"></div></td>');
		}
		if(dayDuringTask <= 0)
		{
			this.table.children('tbody').children('tr:last').append('<td class="ui-gantt-day"></td>');
		}
		for(var i = 0; i < dayAfterTask; i++)
		{
			this.table.children('tbody').children('tr:last').append('<td class="ui-gantt-day"></td>');
		}


		for(var i = this.options.titles.length; i < this._dayBetween(this.options.displayStartDate, this.options.displayFinishDate) + this.options.titles.length; i++)
		{
			if(this.table.children('tfoot').children('tr:first').children('th:nth-child(' + i + ')').is('.ui-gantt-sunday'))
			{
				this.table.children('tbody').children('tr:last').children('td:nth-child(' + i + ')').addClass('ui-gantt-sunday');
			}
			if(this.table.children('tfoot').children('tr:first').children('th:nth-child(' + i + ')').is('.ui-gantt-monday'))
			{
				this.table.children('tbody').children('tr:last').children('td:nth-child(' + i + ')').addClass('ui-gantt-monday');
			}
			if(this.table.children('tfoot').children('tr:first').children('th:nth-child(' + i + ')').is('.ui-gantt-tuesday'))
			{
				this.table.children('tbody').children('tr:last').children('td:nth-child(' + i + ')').addClass('ui-gantt-tuesday');
			}
			if(this.table.children('tfoot').children('tr:first').children('th:nth-child(' + i + ')').is('.ui-gantt-wednesday'))
			{
				this.table.children('tbody').children('tr:last').children('td:nth-child(' + i + ')').addClass('ui-gantt-wednesday');
			}
			if(this.table.children('tfoot').children('tr:first').children('th:nth-child(' + i + ')').is('.ui-gantt-thursday'))
			{
				this.table.children('tbody').children('tr:last').children('td:nth-child(' + i + ')').addClass('ui-gantt-thursday');
			}
			if(this.table.children('tfoot').children('tr:first').children('th:nth-child(' + i + ')').is('.ui-gantt-friday'))
			{
				this.table.children('tbody').children('tr:last').children('td:nth-child(' + i + ')').addClass('ui-gantt-friday');
			}
			if(this.table.children('tfoot').children('tr:first').children('th:nth-child(' + i + ')').is('.ui-gantt-saturday'))
			{
				this.table.children('tbody').children('tr:last').children('td:nth-child(' + i + ')').addClass('ui-gantt-saturday');
			}
		}

	},

	_dayBetween: function(startDate, finishDate) {

		var startDate = startDate.split('-');
		var startYear = parseInt(startDate[0], 10);
		var startMonth = parseInt(startDate[1], 10) - 1;
		var startDay = parseInt(startDate[2], 10);

		var finishDate = finishDate.split('-');
		var finishYear = parseInt(finishDate[0], 10);
		var finishMonth = parseInt(finishDate[1], 10) - 1;
		var finishDay = parseInt(finishDate[2], 10);

		var difference = new Date();
		difference.setTime(new Date(finishYear, finishMonth, finishDay).getTime() - new Date(startYear, startMonth, startDay).getTime());

		return difference.getTime()/1000/3600/24;

	},

});

$.extend($.ui.gantt, {
	version: "1.7.2",
	defaults: {
		titles: ['Id', 'Task', 'Compl.(%)', 'Start Date', 'Finish Date'],
		tasks: [],
		displayStartDate: null,
		displayFinishDate: null,
		language: 'en'
	}
});

})(jQuery);
