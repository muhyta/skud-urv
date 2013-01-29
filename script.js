


function start() {
	document.getElementById('wait_block').style.visibility = "visible";
	document.getElementById('wait').style.visibility = "visible";
}


function subf(fm){
	var hr=document.getElementById(fm);
	fm.submit();}

function ClassSearchBar (dInput, sDefaultValue, sCssFilled, sCssEmpty, dTextContainer)
{
	// Конфигурация
	this.iMinLength = 2;    // Минимальная длина слова, после которой начинается поиск
	this.iTypeDelay = 300;  // Задержка в миллисекундах, после которой начинается поиск, введена, чтобы не перегружать браузер ложными запросами во время его печати
	this.iHighlightDelay = 300; // Интервал между итеррациями подсветки результатов
	this.iResultsInPack = 10;   // Максимальное чилсло результатов подсвечиваемых за один заход
	this.sPreviousValue = '';   // Предыдущее значение строки поиска для отслеживания изменений во вводе и фильтрации нажатия управляющих клавиш
	var oThis = this;
	this.sSearchText = document.getElementById('constitution_body').innerHTML.replace(/<[^>]*>/ig, '').replace(/[^А-я 0-9]/ig, ' ').replace(/(\s| ){2,}/ig, ' ').toUpperCase();
	this.dInput = dInput;
	this.sValue = sDefaultValue;
	this.sCssFilled = sCssFilled;
	this.sCssEmpty = sCssEmpty;
	this.dTextContainer = dTextContainer;
	this.tTimer = null;
	this.tHighliteTimer = null;
	this.bClearText = true; // Флаг, указывающий на то, что в тексте нет разметки поиска
	this.aArticleIndex = {};
	this.aSearchResults = {};
	this.dPreviousHighlited = null;
	this.iTotalResults = null;
	this.iLastHighlited = null;
	this.dInfoscroller = null;
	this.aParagraphs = [];
	this.iLastFocused = null;
	this.aParagrapsResult = [];
	this.aParagraphsForCleanup = [];
	var buttons = this.dInput.parentNode.getElementsByTagName('a');
	for (var i in buttons)
	{
		if (/next/.test(buttons[i].className)) this.dNextButton = buttons[i];
		if (/previous/.test(buttons[i].className)) this.dPreviousButton = buttons[i];
	}
	var blocks = this.dInput.parentNode.getElementsByTagName('div');
	for (var i in blocks)
	{
		if (/results_count/.test(blocks[i].className)) this.dCounter = blocks[i];
	}

	// Составление индекса слов для поиска по алфавитному указателю
	this.analizeIndex = function ()
	{
		var start = new Date();
		var aDivs = document.getElementById('index_content').getElementsByTagName('div');
		for (var i in aDivs) // Сканирование контейнеров item
		{
			if (/item/.test(aDivs[i].className))
			{
				var aContent = aDivs[i].getElementsByTagName('div');
				var termin = aContent[0].getElementsByTagName('a')[0].innerHTML.replace(/<.*?>/ig, '');
				for (var j in aContent) // Сканирование вложенных контейнеров
				{
					if (/pointer/.test(aContent[j].className))
					{
						var item = aContent[j].firstChild.nodeValue;
						var aArticles = aContent[j].getElementsByTagName('a');
						for (var t in aArticles)
						{
							if (!aArticles[t].href) continue;
							var article = aArticles[t].href.match(/#(\w+(-\d+)?)/)[1];
							this.aArticleIndex[article] += ' '+termin+' '+aContent[j].innerHTML.replace(/(<.*?>|ст. \d+|[.,:])/ig, '').replace(/\s\d+/, '');
						}
					}
				}
			}
		}
		for (i in this.aArticleIndex) this.aArticleIndex[i] = this.aArticleIndex[i].replace(/[^А-я 0-9]/ig, ' ').replace(/(\s| ){2,}/ig, ' ').toUpperCase();
		end = new Date()
	}
	this.analizeIndex();

	// Кроссбраузерный привязыватель событий :)
	this.addEvent = function (dElement, sEventType, fHandler)
	{
		if (dElement.attachEvent) dElement.attachEvent ('on' + sEventType, fHandler);
		if (dElement.addEventListener) dElement.addEventListener (sEventType, fHandler, false);
	}

	// Функции управления кнопками
	this.disableNextButton = function ()
	{
		this.dNextButton.className = this.dNextButton.className.replace(/\s*next_active\s*/, '');
		this.dNextButton.onclick = null;
	}
	this.disablePreviousButton = function ()
	{
		this.dPreviousButton.className = this.dPreviousButton.className.replace(/\s*previous_active\s*/, '');
		this.dPreviousButton.onclick = null;
	}
	this.enableNextButton = function ()
	{
		if (!/next_active/.test(this.dNextButton.className)) this.dNextButton.className += ' next_active';
		this.dNextButton.onclick = function() {return oThis.findNext()};
	}
	this.enablePreviousButton = function ()
	{
		if (!/previous_active/.test(this.dPreviousButton.className)) this.dPreviousButton.className += ' previous_active';
		this.dPreviousButton.onclick = function() {return oThis.findPrevious()};
	}
	this.disableButtons = function ()
	{
		this.disableNextButton();
		this.disablePreviousButton();
	}

	// Функция поиска по основному тексту
	this.searchParagraphs = function (regex)
	{
		var count = 0;
		var length = this.aParagraphs.length;
		for (var i = 0; i < length; i++)
		{
			var tested_text = this.aParagraphs[i].body.innerHTML;
			regex.lastIndex = 0;
			var condition = regex.test(tested_text);
			if (condition)
			{
				this.aParagrapsResult.push(this.aParagraphs[i]);
				count++;
			}
			else
			{var condition1 = regex.test(tested_text);
			}
		}
		return count;
	}

	// Подсветка следующего результата
	this.findNext = function(dElement)
	{
		var scroll_element = document.getElementById('main_scroll_wrap');
		var jump = true;
		if (this.iLastFocused === null || this.iLastFocused + 2 > this.aSearchResults.length) this.iLastFocused = 0;
		else this.iLastFocused++;
		if (this.dPreviousHighlited && this.dPreviousHighlited.className != '') this.dPreviousHighlited.className = '';
		if (dElement)
		{
			var top = getTopOffset(dElement)+60;
			for (var i in this.aSearchResults)
			{
				this.aSearchResults[i];
				if (getTopOffset(this.aSearchResults[i]) > top)
				{
					this.iLastFocused = i-1;
					jump = false;
					break;
				}
			}
		}
		this.dPreviousHighlited = this.aSearchResults[this.iLastFocused];
		this.dPreviousHighlited.className = 'active';
		if (jump) scroll_element.scrollTop = getTopOffset(this.dPreviousHighlited) - 30;
		this.dCounter.innerHTML = this.dCounter.innerHTML.replace (/\d+ (из \d+)/, (this.iLastFocused*1+1)+' $1');
		if (this.iLastFocused <= 0) this.disablePreviousButton();
		else this.enablePreviousButton();
		this.dInput.focus();
	}

	// Подсветка предыдущего результата
	this.findPrevious = function ()
	{
		var scroll_element = document.getElementById('main_scroll_wrap');
		if (!this.iLastFocused)
		{
			this.disablePreviousButton();
			return false;
		}
		else this.iLastFocused--;
		if (this.dPreviousHighlited && this.dPreviousHighlited.className != '') this.dPreviousHighlited.className = '';
		this.dPreviousHighlited = this.aSearchResults[this.iLastFocused];
		this.dPreviousHighlited.className = 'active';
		scroll_element.scrollTop = getTopOffset(this.dPreviousHighlited) - 10;
		this.dCounter.innerHTML = this.dCounter.innerHTML.replace (/\d+ (из \d+)/, (this.iLastFocused+1)+' $1');
		if (this.iLastFocused <= 0) this.disablePreviousButton();
		else this.enablePreviousButton();
	}

	// Добавление элемента в инфоскроллер
	this.markInfoscroller = function (dElement, dInfoscroller, iDocHeight)
	{
		this.iTotalResults++;
		var iNumber = this.iTotalResults;
		var marker = document.createElement('a');
		var href = dElement.getElementsByTagName('a');
		if (href[0]) marker.href = '#' + href[0].name;
		marker.onclick = function(){oThis.findNext(dElement)};
		var top = getTopOffset(dElement);
		var percent = top/iDocHeight*100;
		marker.style.top = percent + '%';
		dInfoscroller.appendChild(marker);
	}

	// Добавление элементов в массив подсветки
	this.selectHightited = function ()
	{
		this.aSearchResults = document.getElementsByTagName('ins');
		this.dCounter.innerHTML = this.dCounter.innerHTML.replace (/(\d+) из (\d+)/, '$1 из ' + this.aSearchResults.length);
		this.enableNextButton();
	}

	// Очисктка результатов предыдущего поиска
	this.clearSearchResult = function ()
	{
		this.iTotalResults = 0;
		var count = 0;
		this.dCounter.parentNode.style.display = 'none';
		if (this.dPreviousHighlited && this.dPreviousHighlited.className != '') this.dPreviousHighlited.className = null;
		var paragraph = null;
		while (paragraph = this.aParagraphsForCleanup.pop())
		{
			paragraph.innerHTML = paragraph.innerHTML.replace(/<\/?ins.*?>/ig, '');
			restoreReferences(paragraph); // КОСТЫЛЬ для восстановления ссылок на сноски после изменения тела документа
			count++;
		}
		this.aParagrapsResult = [];
		this.bClearText = true;
		this.disableButtons();
		if (this.dInfoscroller)
		{
			this.dInfoscroller.parentNode.removeChild(this.dInfoscroller);
			this.dInfoscroller = null;
		}
		this.dPreviousHighlited = null;
		this.sPreviousValue = null;
		var end = new Date();
	}

	this.startSearch = function()
	{
		var clear_value = this.dInput.value.replace(/[^А-я0-9]/g, '');
		if (clear_value.length == 0 && !this.bClearText) this.clearSearchResult();
		if ((clear_value.length < this.iMinLength && !/^\d+$/.test(this.dInput.value))||clear_value==this.sPreviousValue)
		{
			return false;
		}
		this.sPreviousValue = clear_value;
		if (!this.bClearText)
		{
			this.clearSearchResult();
		}
		if (!IE_VERSION || IE_VERSION>7)
		{
			this.dInfoscroller = document.createElement('div');
			this.dInfoscroller.className="infoscroller_container";
			this.dInfoscroller = document.getElementsByTagName('body')[0].appendChild(this.dInfoscroller);
		}
		this.iLastFocused = null;
		if (this.tHighliteTimer)
		{
			clearTimeout(this.tHighliteTimer);
			this.tHighliteTimer = null;
		}

		this.iLastHighlited = 0;
		var additional_results = [];
		var count_regex = this.dInput.value.replace(/<[^>]*>/ig, '').replace(/[^А-я 0-9]/ig, ' ').replace(/(\s| ){2,}/ig, ' ').toUpperCase();
		count_regex = new RegExp('('+count_regex+')', 'ig');
		var string = this.dInput.value;
		string = string.replace(/(\s| )*(.*?)(\s| )*/, '$2'); // Обрезаются краевые пробелы
		string = string.split(/[\s ]+/).join('[\\s| ]*?');
		var regex = new RegExp('('+string+')', 'ig');   //

		var count = this.searchParagraphs(regex);
		for (var i in this.aArticleIndex)
		{
			if (count_regex.test(this.aArticleIndex[i])) additional_results.push(i);
		}
		if (count + additional_results.length <= 0)
		{
			this.clearSearchResult();
			this.dCounter.innerHTML = 'не найдено';
			this.bClearText = false;
			this.dCounter.parentNode.style.display = 'block';
			return false;
		}
		this.dCounter.innerHTML = '1 из ' + (additional_results.length + count);
		this.dCounter.parentNode.style.display = 'block';
		this.highliteAdditional(additional_results);
		this.highliteByTimer(regex);
		this.tHighliteTimer = setTimeout(function(){oThis.highliteByTimer(regex)}, this.iHighlightDelay*3);
		this.findNext();
	}

	this.highliteByTimer = function (regex)
	{
		var scroll_element = document.getElementById('main_scroll_wrap');
		var doc_height = scroll_element.scrollHeight;
		var length = this.aParagrapsResult.length;
		var count = 0;
		for (var i = this.iLastHighlited; i < length; i++)
		{
			this.aParagraphsForCleanup.push(this.aParagrapsResult[i].body);
			this.aParagrapsResult[i].body.innerHTML = this.aParagrapsResult[i].body.innerHTML.replace(regex, '<ins>$1</ins>');
			restoreReferences(this.aParagrapsResult[i].body); // КОСТЫЛЬ для восстновления ссылок на сноски
			if (!IE_VERSION || IE_VERSION>7) this.markInfoscroller(this.aParagrapsResult[i].body, this.dInfoscroller, doc_height)
			count++;
			this.iLastHighlited = i+1;
			if (count >= this.iResultsInPack)
			{
				break;
			}
		}
		if (this.iLastHighlited < length - 1) this.tHighliteTimer = setTimeout(function(){oThis.highliteByTimer(regex)}, this.iHighlightDelay);
		else this.tHighliteTimer = null;
		this.selectHightited();
	}

	this.highliteAdditional = function (additional_results)
	{
		var scroll_element = document.getElementById('main_scroll_wrap');
		var doc_height = scroll_element.scrollHeight;
		var length = additional_results.length;
		if (length>0)
		{
			for (var i = 0; i < length; i++)
			{
				var elements = document.getElementsByName(additional_results[i]);
				var header = elements[0]&&elements[0].parentNode;
				if (!header) continue;
				this.aParagraphsForCleanup.push(header);
				header.innerHTML = '<ins>'+header.innerHTML+'</ins>';
				if (!IE_VERSION || IE_VERSION>7) this.markInfoscroller(header, this.dInfoscroller, doc_height)
			}
		}

		this.bClearText = false;
	}

	this.onFocus = function()
	{
		if (this.dInput.value == this.sValue) this.dInput.value = '';
		this.dInput.parentNode.parentNode.className = this.sCssFilled;
	}

	this.onKeyDown = function()
	{
		this.dInput.parentNode.parentNode.className = this.sCssFilled;;
	}

	this.onKeyUp = function(event)
	{
		var oThis = this;
		if (this.tTimer) clearTimeout(this.tTimer);
		if (!event) event = window.event;
		if (event.keyCode == 13 && this.dPreviousHighlited)
		{
			this.findNext();
			return false;
		}
		this.tTimer = setTimeout(function(){return oThis.startSearch()}, this.iTypeDelay);
	}

	this.onBlur = function()
	{
		if (this.dInput.value == '' || this.dInput.value == this.sValue)
		{
			this.dInput.value = this.sValue;
			this.dInput.parentNode.parentNode.className = this.sCssEmpty;
		}
		else
		{
			this.dInput.parentNode.parentNode.className = this.sCssFilled;
		}
	}

	this.selectInputText = function ()
	{
		var start = 0;
		var end = this.dInput.value.length;
		this.dInput.focus();
		if (this.dInput.setSelectionRange) this.dInput.setSelectionRange(start, end);
	}

	this.getParagraphSource = function (sTag)
	{
		var paragraphs = this.dTextContainer.getElementsByTagName(sTag);
		for (var i in paragraphs)
		{

			if (paragraphs[i].nodeType != 1) continue;
			paragraphs[i].innerHTML = '<span>'+paragraphs[i].innerHTML+'</span>';
			this.aParagraphs.push({'body': paragraphs[i].getElementsByTagName('span')[0], 'type': sTag});
		}
	}

	this.getParagraphSource('h2');
	this.getParagraphSource('h3');
	this.getParagraphSource('p');

	link = location.href.match(/#(.*)/);
	if (link && link[1]) location.href = '#' + link[1]; //Устраняет прыжок куда-попало при удалении якоря из ссылки в процессе модификации дерева сайта

	this.addEvent(this.dInput, 'focus', function() {return oThis.onFocus()});
	this.addEvent(this.dInput, 'click', function(){oThis.selectInputText()});
	this.addEvent(this.dInput, 'blur',  function() {return oThis.onBlur()});
	this.addEvent(this.dInput, 'keydown', function() {return oThis.onKeyDown()});
	this.addEvent(this.dInput, 'keyup', function(event) {return oThis.onKeyUp(event)});


	if (this.dInput.value == '') this.onBlur();

	return this;
}