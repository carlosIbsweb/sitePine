(function($) {

    $.fn.santSlide = function(options) {


        var opt = $.extend({
            json: '',
            sliderId:'',
            folder: '',
            duration: 2000,
            slideLines: '',
            random: false,
            limit: '',
            slider: 0,
            columns:3
        }, options);

        const slide = function() {
            
            $mjson = JSON.parse(opt.json) //.slice(0,6)

            //Limite de imagens Number.isInteger(opt.limit)
            if(opt.limit != ''){
                $mjson = JSON.parse(opt.json).slice(0,opt.limit);
            }

            //Columns.
            $columns = 100 / opt.columns+'%'; 

            //Imagens aleátorias.
            if(opt.random != 0){    
                $mjson = shuffle($mjson);
            }

           
            $divider = opt.slideLines == '' ? 1 : Math.ceil($mjson.length / opt.slideLines);
            $output = [];
            $joga = [];

            $.each(chunk($mjson, $divider), function(ind, img) {

                $joga.push(ind);
                $output.push('<ul id="ss' + ind + '">');

                $.each(img, function(imgind, item) {
                    if (imgind == 0) {
                        $output.push('<li id="' + item.id + '" style="width:'+$columns+'" class="active"><a  href="' + opt.folder + '/large/' + item.image + '" title="'+item.title+'"><img src="' + opt.folder + '/thumbs/' + item.image + '" alt="' +item.title+ '"></a></li>');
                    } else {
                        $output.push('<li id="' + item.id + '" style="display:none; width:'+$columns+' class="simagetemp" data-image="'+ opt.folder + '/thumbs/' + item.image + '" data-alt="'+item.title+'"></li>');
                    }
                })
                $output.push('</ul>');
            })

            $ja = 0;
            $nslide = function() {

                $uid = '#ss' + $joga[$ja++];
                if ($($uid).find('li.active').next().length != 0) {
                    $($uid).find('li.active').css('position', 'absolute').next('li').css('display', 'inline-block');
                }

                if ($($uid).find('li.active').next().length == 0) {
                    $($uid).find('li').eq(0).css('position', 'absolute').fadeIn('milliseconds')
                }

                $($uid).find('li.active').fadeOut('milliseconds', function() {

                    $(this).removeClass('active').css('position', '').next('li').fadeIn('milliseconds').addClass('active');

                    if ($(this).next().length == 0) {
                        $($uid).find('li').eq(0).addClass('active').css('position', '').css('display', 'inline-block')
                    }
                })

                if ($ja == $joga.length) {
                    $ja = 0;
                }
            }

            
            $( window ).load(function(){
                $imgsTemp = $('.simagetemp');

                $.each($imgsTemp,function(){
                    $imgTemp = $(this).data('image');
                    $imgAlt  = $(this).data('alt');

                    $(this).append('<img src="'+$imgTemp+'" alt="'+$imgAlt+'" >');
                })

                //Iniciar efeito slider.
                if(opt.slider == 1)
                {
                    $temp = setInterval($nslide, opt.duration)
                }
            })

            if(opt.slider == 1){

                $(document).on('hover', '.santSlide li', function(e) {
                    if (e.type == "mouseenter") {
                        //$(this).removeClass('active')
                        clearInterval($temp)
                    } else if (e.type == "mouseleave" && opt.slider) {
                        $temp = setInterval($nslide, opt.duration)
                    }
                });
            }

            //Saída do meu slider.
            $(opt.sliderId).append($output.join(''));
        }

        //modal do meu slider.
        const modal = function(el) {

            $winH = $(window).height() - 80;

            //Template
            $templat = '<div class="santBg santClose" style="display:none"></div><div class="santContent" style="display:none"><span class="closeBani santClose" style="display:none">X</span><div class="santImg"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"></circle></svg></div></div>';

            if ($('.santBg').length == 0) {
                $('body').prepend($templat);

                $('.santBg').fadeIn(function() {
                    $('.santContent').fadeIn(function() {
                        $('<img src="' + $(el).find('img').attr('src').replace('thumbs', 'large') + '" />').load(function() {
                            $('.santImg').hide().fadeIn().addClass('zoomIn animated').html('<img src="' + $(this).attr('src') + '" style="max-height:' + $winH + 'px"/>')
                            $('.santContent').find('.closeBani').css('display', 'block').addClass('bounceInDown animated');
                        })
                    });
                })
            }
        }

        $(document).on('click', '.santClose', function() {
            $('.santImg').addClass('zoomOut animated');
            $('.santClose').removeClass('bounceInDown').addClass('bounceOutUp')
            setTimeout(function() {
                $('.santContent').fadeOut(function() {
                    $('.santBg').fadeOut(function() {
                        $(this).remove();
                        $('.santContent').remove();
                    })
                })
            }, 500)
        })

        //Separar array em pedaços
        function chunk(arr, size) {
            return arr.reduce((chunks, el, i) => {
                if (i % size === 0) {
                    chunks.push([el])
                } else {
                    chunks[chunks.length - 1].push(el)
                }
                return chunks
            }, [])
        }

        //Random array
        function shuffle(array) {
          var currentIndex = array.length, temporaryValue, randomIndex;

          // While there remain elements to shuffle...
          while (0 !== currentIndex) {

            // Pick a remaining element...
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex -= 1;

            // And swap it with the current element.
            temporaryValue = array[currentIndex];
            array[currentIndex] = array[randomIndex];
            array[randomIndex] = temporaryValue;
          }

          return array;
        }

        slide();

        //$(document).on('click', '.santSlide li', function() {
          //  modal($(this))
        //})

        $(window).resize(function() {
            $winH = $(window).height() - 80;
            if ($('.santBg').length == 1) {
                $('.santContent').find('img').css("max-height", $winH)
            }
        })
    };

}(jQuery));