// daGallery v2.0, http://cvek.ru/portfolio/development/
// отредактирован для Muz-Tracker.net
var daGalContainer;  //Контейнер для галереи
var daGalBg;        //Фон всей галереи
var daImgContainer;//Контейнер для картинки и её описания
var daImg;        //Текущая картинка
var daImgList;   //Массив всех ссылок на картинки
var daCurImg;   //Порядковый номер текущей картинки в галерее
var isStarted; //Запущена ли уже галерея

function initDAGal(){
  $('.daGallery a[rel]').click(startDAGal);
  isStarted = false;
}

function startDAGal() {
  if (isStarted) return false;
  isStarted = true;
  if ( $(this).attr('rel') == '' ) return; // Если нет атрибута rel - галерея не запускается
  daImgList = $('.daGallery a[rel="'+$(this).attr('rel')+'"]'); //Все ссылки на картинки данной галереи
  for (daCurImg = i = 0; $(daImgList[i]).attr('href') != $(this).attr('href'); daCurImg = ++i);

  $('body')
    .append('<table id="daGalContainer"><tr><td class="daTools"><a href="#" class="daPrev"></a><a href="#" target="_blank" class="daLupa"></a><a href="#" class="daNext"></a><a href="#" class="daClose"></a></td></tr><tr><td class="daImg"></td></tr></table>')/// by 7Max7
    .append('<div id="daGalBg"></div>');

  //Кешируем
  daGalContainer = $('#daGalContainer');
  daGalBg = $('#daGalBg').click( function(){closeDAGal(); return false});
  daImgContainer = $('#daGalContainer .daImg');
  wnd = $(window);

  showImgDAGal($(this));//Создаём картинку

  daGalBg.css('opacity',0).fadeTo('slow',0.5);
  daGalContainer.find('.daPrev').click( function(){switchImgDAGal(--daCurImg); return false});
  daGalContainer.find('.daNext').click( function(){switchImgDAGal(++daCurImg); return false});
  daGalContainer.find('.daPrev').attr('title','Prev');
  daGalContainer.find('.daNext').attr('title','Next');
  daGalContainer.find('.daLupa').attr('title','Full Size');
  daGalContainer.find('.daClose').attr('title','Exit').click(function(){closeDAGal(); return false});
  wnd.resize(wResizeDAGal);
  wResizeDAGal();
  return false;
}

function wResizeDAGal(){
  //Корректируем размер задника
  wnd = $(window);
  daGalContainer.css('top', (wnd.height()/2 - daGalContainer.height()/2) + wnd.scrollTop());
  daGalContainer.css('left',(wnd.width()/2 - daGalContainer.width()/2) + wnd.scrollLeft());
}

function switchImgDAGal(num){
  daGalContainer.find('.daPrev').hide();
  daGalContainer.find('.daNext').hide();
  daGalContainer.find('.daLupa').hide();
  daImgContainer.find('img').remove();//Убираем старую картинку
  daImgContainer.find('.daDesc').remove();//Убираем описание
  daImgContainer.find('b').remove();
  wResizeDAGal();
  showImgDAGal($(daImgList[daCurImg]));
}

function showImgDAGal(el){
  //Создаём картинку
  daImgContainer.addClass('daGalLoad');
  daNewImg = new Image();
  $(daNewImg)
    .error( function(){
      contrlsOnDAGal();
      daImgContainer.removeClass('daGalLoad');
      daImgContainer.html('<b>Image no founded<br/>'+el.attr('href')+'</b>');
      wResizeDAGal();
    })
    .load( function(){
      contrlsOnDAGal();
      daImgContainer.removeClass('daGalLoad');
      $(daNewImg).attr('alt', el.find('img').attr('alt') );
      daGalContainer.hide();
      daImgContainer.append(daNewImg).append('<div class="daDesc">'+el.attr('title')+'</div>');
      daImg = daImgContainer.find('img');
      daGalContainer.fadeIn();
      //Устанавливаем свойства картинок
      wnd = $(window);

      if (daImg.width() > wnd.width()){
        newHeight = daImg.height()* (wnd.width()-60) / daImg.width();
        daImg.width( wnd.width()-60 ).height( newHeight );
        daGalContainer.find('.daLupa').show().attr('href', el.attr('href'));
      }
      if (daImg.height() > (wnd.height()-120)){
        newWidth = daImg.width()* (wnd.height()-120) / daImg.height();
        daImg.height( wnd.height()-120 ).width( newWidth );
        daGalContainer.find('.daLupa').show().attr('href', el.attr('href'));
      }
      daGalContainer.find('.daTools').width( daImg.width() );
      
      wResizeDAGal();
    })
    .attr('src', el.attr('href'));
}

function closeDAGal(){
  $(window).unbind('resize');
  daGalBg.remove();
  daGalContainer.remove();
  isStarted = false;
}

function contrlsOnDAGal(){
  if (daCurImg > 0) daGalContainer.find('.daPrev').show();
  if (daCurImg < ($(daImgList).length - 1)) daGalContainer.find('.daNext').show();
}