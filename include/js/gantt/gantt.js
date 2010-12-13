/*
 * jquery.gantt 0.3
 * By Maro(Hidemaro Mukai)  - http://www.maro-z.com/
 * Copyright (c) 2008 Hidemaro Mukai
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 */
(function($) {
	$.extend({
		gantt : new function(){
			var gnt = this;
			var _options;
			gnt.constract = function(c, o){
				gnt._options = $.extend({}, $.fn.defaults_gantt, o);		
				var i;
				var p = {};

				if (gnt._options.tasks == undefined){
					gnt._options.tasks = {};
				}
				if (gnt._options.titles == undefined){
					gnt._options.titles = new Array('Task');
				}
				if (gnt._options.titles instanceof String){
					gnt._options.titles = new Array(gnt._options.titles);
				}
				p.title_length = gnt._options.titles.length;
				if (gnt._options.type == undefined){
					p.type = gnt._options.defaultType;
				}else{
					p.type = gnt._options.type;
				}
				if (gnt._options.range == undefined){
					p.range = gnt._options.defaultRange;
				}else{
					p.range = gnt._options.range;
				}
				p.tasks = [];
				p = gnt.addTaskData(p, gnt._options.tasks);
				gnt.setDates(p, gnt._options.from, gnt._options.to);
				var gnt_tbl = $('<table border="0" cellpadding="0" cellspacing="0" class="GNT_tbl"></table>');
				var gnt_Header = $('<tr></tr>');
				for(i=0;i<gnt._options.titles.length;i++){
					gnt_Header.append('<th class="GNT_head">'+gnt._options.titles[i]+'</th>');
				}
				gnt_Header.append('<th class="GNT_cal"></th>');
				gnt_tbl.append(gnt_Header);
				c.html(gnt_tbl);
				c.each(function(){
					var gnt_tbl = $('table.GNT_tbl', this);
					$.data(gnt_tbl.get()[0], 'params', p);
				});
				gnt.write(c);
			}
			gnt.write = function(c){
				c.each(function(){
					var gnt_tbl = $('table.GNT_tbl', this);
					var p = $.data(gnt_tbl.get()[0], "params");
					var cal = $('<ul></ul>');
					var calW = 0;
					var liW = 0;
					var wd = 0;
					var y = p.from.y;
					var m = p.from.m;
					var d = p.from.d;
					switch(p.type){
						case 'month':
							var listUl = $('<ul class="GNT_calList GNT_yearList"></ul>');
							var remain = gnt.getRemainMonth(p.from.y, p.from.m, p.to.y, p.to.m);
							if (remain > gnt._options.rangeLimit){
								remain = gnt._options.rangeLimit;
							}
							for(y=p.from.y;y<=p.to.y;y++){
								year = $('<li><div class="GNT_year">'+y+'</div><div class="GNT_date GNT_date_'+gnt._options.lang+'"></div></li>');
								wd = (12-(m-1));
								if (wd > remain){
									wd = remain;
								}
								liW = wd * gnt._options.graphSize;
								$('.GNT_year', year).css({
									'width': liW
								});
								$('.GNT_date', year).css({
									'width': liW,
									'background-position': 0-(m-1)*gnt._options.graphSize+'px 0'
								});
								listUl.append(year);
								remain -= wd;
								calW += liW;
								if (remain <= 0){
									break;
								}
								m=1;
							}
							cal.append(listUl.wrap('<li></li>'));
						break;
						default:
							var listUl = $('<ul class="GNT_calList GNT_monthList"></ul>');
							var month = null;
							var remain = gnt.getRemainDate(p.from.y, p.from.m, p.from.d, p.to.y, p.to.m, p.to.d);
							if (remain > gnt._options.rangeLimit){
								remain = gnt._options.rangeLimit;
							}
							for(y=p.from.y;y<=p.to.y;y++){
								for(m=m;m<=12;m++){
									month = $('<li><div class="GNT_month">'+gnt.getMonthName(y, m, d)+'</div><div class="GNT_date GNT_date_'+gnt._options.lang+'"></div></li>');
									wd = gnt.getRemainDate(y, m, d, y, m+1, 0);
									if (wd > remain){
										wd = remain;
									}
									liW = wd * gnt._options.graphSize;
									$('.GNT_month', month).css({
										'width': liW
									});
									$('.GNT_date', month).css({
										'width': liW,
										'background-position': 0-(d-1)*gnt._options.graphSize+'px 0'
									});
									listUl.append(month);
									remain -= wd;
									calW += liW;
									if (remain <= 0){
										break;
									}
									d = 1;
								}
								m = 1;
							}
							cal.append(listUl.wrap('<li></li>'));
							cal.append('<li class="GNT_day GNT_day_'+gnt._options.lang+'"></li>');
							$('.GNT_day', cal).css({
								'background-position': p.dayBgPos+'px 0'
							});
						break;
					}
					cal.css({
						'width': calW
					});
					$('th.GNT_cal', gnt_tbl).html(cal);
				});
				gnt.writeTask(c);
			}
			gnt.clearTask = function(c){
				$('tr.GNT_task', c).remove();
			}
			gnt.writeTask = function(c){
				gnt.clearTask(c);
				c.each(function(){
					var gnt_tbl = $('table.GNT_tbl', this);
					var p = $.data(gnt_tbl.get()[0], "params");
					for(var i=0;i<p.tasks.length;i++){
						var trObj = $('<tr class="GNT_task"></tr>');
						for(var n=0;n<p.title_length;n++){
							trObj.append('<td class="GNT_title">'+p.tasks[i].titles[n]+'</td>');
						}
						trObj.append('<td class="GNT_chart"></td>');
						$(".GNT_chart", trObj).addClass('GNT_chart_'+p.type);
						$(".GNT_chart", trObj).css({
							'background-position': p.dayBgPos+'px 0'
						});
						var s = p.tasks[i].start_date;
						var e = p.tasks[i].end_date;
						if (!s || !e){
							$(".GNT_chart", trObj).append('<span class="GNT_error">Error</span>');
							gnt_tbl.append(trObj);
							continue;
						}
						if (!gnt.compareDate(s, p.to) || !gnt.compareDate(p.from, e)){
							gnt_tbl.append(trObj);
							continue;
						}
						if (!gnt.compareDate(p.from, s)){
							s = p.from;
						}
						if (!gnt.compareDate(e, p.to)){
							e = p.to;
						}
						$(".GNT_chart", trObj).append('<div class="GNT_bar"><div class="GNT_bar_body">&nbsp;</div></div>');
						if (p.tasks[i].text){
							$(".GNT_bar_body", trObj).html(p.tasks[i].text);
						}
						if (p.tasks[i].color){
							$(".GNT_bar_body", trObj).css({
								'background-color': p.tasks[i].color
							});
						}
						switch(p.type){
							case 'month':
								var barLeft = gnt.diffMonth(p.from.y, p.from.m, s.y, s.m)*gnt._options.graphSize;
								var barWidth = gnt.getRemainMonth(s.y, s.m, e.y, e.m)*gnt._options.graphSize;
							break;
							default:
								var barLeft = gnt.diffDate(p.from.y, p.from.m, p.from.d, s.y, s.m, s.d)*gnt._options.graphSize;
								var barWidth = gnt.getRemainDate(s.y, s.m, s.d, e.y, e.m, e.d)*gnt._options.graphSize;
							break;
						}
						$(".GNT_bar", trObj).css({
							'left': barLeft+'px',
							'width': barWidth+'px'
						});
						gnt_tbl.append(trObj);
					}
				});
			}
			gnt.addTask = function(c, t){
				if (!(t instanceof Array)){
					t = Array(t);
				}
				c.each(function(){
					var gnt_tbl = $('table.GNT_tbl', this);
					var p = $.data(gnt_tbl.get()[0], "params");
					p = gnt.addTaskData(p, t);
					$.data(gnt_tbl.get()[0], 'params', p);
				});
				gnt.writeTask(c);
			}
			gnt.addTaskData = function(p, t){
				if (!t){
					return p;
				}
				if (!(t instanceof Array)){
					t = Array(t);
				}
				for(var i=0;i<t.length;i++){
					var task = {};
					if (!t[i].titles){
						t[i].titles = 'Task'+(p.tasks.length+1);
					}
					if (!(t[i].titles instanceof Array)){
						t[i].titles = Array(t[i].titles);
					}
					t[i].start_date = gnt.dateDiv(t[i].start_date);
					t[i].end_date = gnt.dateDiv(t[i].end_date);
					p.tasks.push(t[i]);
				}
				return p;
			}
			gnt.remove = function(c){
				$('table.GNT_tbl', c).remove();
			}
			gnt.setPeriod = function(c, f, t){
				c.each(function(){
					var gnt_tbl = $('table.GNT_tbl', this);
					var p = $.data($('table.GNT_tbl', this).get()[0], "params")
					p = gnt.setDates(p, f, t);
					$.data($('table.GNT_tbl', this).get()[0], "params", p);
				});
				gnt.write(c);
			}
			gnt.setType = function(c, type){
				c.each(function(){
					var gnt_tbl = $('table.GNT_tbl', this);
					var p = $.data($('table.GNT_tbl', this).get()[0], "params");
					p.type = type;
					p = gnt.setDates(p);
					$.data($('table.GNT_tbl', this).get()[0], "params", p);
				});
				gnt.write(c);
			}
			gnt.setDates = function(p, f, t){
				if (f && f.match(/[+-]/)){
					var s = f.substr(0, 1);
					var r = parseInt(f.substr(1), 10);
					if (!r){
						r = p.range;
					}
					if (s == '-'){
						r *= -1;
					}
					f = gnt.getCalcDate(p.from, r, p.type);
					t = gnt.getCalcDate(f, p.range-1, p.type);
				}else{
					f = gnt.dateDiv(f);
					if (!f){
						f = gnt.dateDiv('now');
					}
					t = gnt.dateDiv(t);
					if (!gnt.compareDate(f, t)){
						t = gnt.getCalcDate(f, p.range-1, p.type);
					}
				}
				p.from = f;
				p.to = t;
				p.dayBgPos = 0-gnt.getDayNum(p.from.y, p.from.m, p.from.d)*gnt._options.graphSize;
				return p;
			}
			gnt.getDayNum = function(y, m, d){
				var dateObj = new Date(y, m-1, d);
				return dateObj.getDay();
			}
			gnt.getMonthName = function(y, m, d){
				var dateObj = new Date(y, m-1, d);
				var month = [
					'January',
					'February',
					'March',
					'April',
					'May',
					'June',
					'July',
					'August',
					'September',
					'October',
					'November',
					'December'
				];
				switch (gnt._options.monthNameType){
				case 2:
					return month[dateObj.getMonth()]+' '+y;
					break;
				case 3:
					return month[dateObj.getMonth()].substr(0, 3)+' '+y;
					break;
				default:
					return y+'/'+m;
					break;
				}
				return month[dateObj.getMonth()];
			}
			gnt.getRemainDate = function(fy, fm, fd, ty, tm, td){
				return gnt.diffDate(fy, fm, fd, ty, tm, td)+1;
			}
			gnt.diffDate = function(fy, fm, fd, ty, tm, td){
				var dateFrom = new Date(fy, fm-1, fd);
				var dateTo = new Date(ty, tm-1, td);
				return Math.ceil((dateTo.getTime() - dateFrom.getTime())/(24 * 60 * 60 * 1000));
			}
			gnt.getRemainMonth = function(fy, fm, ty, tm){
				return gnt.diffMonth(fy, fm, ty, tm)+1
			}
			gnt.diffMonth = function(fy, fm, ty, tm){
				return (ty-fy)*12+tm-fm;
			}
			gnt.getCalcDate = function(f, r, t){
				var y = f.y;
				var m = f.m;
				var d = f.d;
				switch(t){
					case 'month':
						m += parseInt(r, 10);
					break;
					default:
						d += parseInt(r, 10);
					break;
				}
				var o = new Date(y, m-1, d);
				return {
					'y' : o.getFullYear(),
					'm' : o.getMonth()+1,
					'd' : o.getDate()
				}
			}
			gnt.compareDate = function(f, t){
				if (!t){
					return false;
				}
				if (new Date(t.y, t.m-1, t.d).getTime() < new Date(f.y, f.m-1, f.d).getTime()){
					return false;
				}
				return true;
			}
			gnt.dateFormat = function(y, m, d){
				if (isNaN(y) || isNaN(m) || isNaN(d)){
					var dateObj = new Date();
				}else{
					var dateObj = new Date(y, m-1, d);
				}
				return {
					'y' : dateObj.getFullYear(),
					'm' : dateObj.getMonth()+1,
					'd' : dateObj.getDate()
				}
			}
			gnt.dateDiv = function(s){
				if (!s){
					return false;
				}
				if (s == 'now'){
					var dateObj = new Date();
					return gnt.dateFormat();
				}else if (s.match(/^\d{8}$/)){
					return gnt.dateFormat(
						parseInt(s.substr(0, 4), 10),
						parseInt(s.substr(4, 2), 10),
						parseInt(s.substr(6, 2), 10)
					);
				}else{
					var delimiter = ['-', '/', '.'];
					for(var i=0;i<delimiter.length;i++){
						if (s.indexOf(delimiter[i]) != -1){
							var ary = s.split(delimiter[i]);
							return gnt.dateFormat(
								parseInt(ary[0], 10),
								parseInt(ary[1], 10),
								parseInt(ary[2], 10)
							);
						}
					}
				}
				return false;
			}
		}
	});
	$.fn.extend({
		gantt: function(o){
			$.gantt.constract($(this), o);
		},
		writeGantt: function(o){
			$.gantt.write($(this));
		},
		addTask: function(t){
			$.gantt.addTask($(this), t);
		},
		setPeriod: function(f, t){
			$.gantt.setPeriod($(this), f, t);
		},
		setType: function(t){
			$.gantt.setType($(this), t);
		},
		removeGantt: function(o){
			$.gantt.remove($(this));
		},
		defaults_gantt: {
			tasks: {},
			defaultType: 'date',
			/* 
			 * j : Japanese
			 * e : english
			 */
			lang: 'j',
			/* 
			 * 1 : YYYY/MM
			 * 2 : January YYYY
			 * 3 : Jan YYYY
			 */
			monthNameType: 1,
			defaultRange: 35,
			rangeLimit: 200,
			graphSize: 12
		}
	});
})(jQuery);