
jQuery(function($) {

    function criarGrupos() {
        return new Promise(function(resolve) {
            $('[data-group]').each(function() {
                let classGroup = $(this).data('group');
                const parentRow = $(this).closest('.aw-form-row');

                if ($('.' + classGroup).length === 0) {
                    parentRow.eq(0).wrap(`<div class="multi ${classGroup}" data-multi="${classGroup}"></div>`);
                }
                parentRow.appendTo('.' + classGroup);
            });

            resolve();
        });
    }

    gerarMulti = function geraMulti(sel) {
    
        const distinctGroups = [...new Set($('[data-group]').map(function() {
            return $(this).attr('data-group');
        }).get())];


        for (const group of distinctGroups) {
            let rows = $(`.multi.${group}`).toArray();
            let i = 1;
            for (let row of rows) {
                let names = $(row).find('[name]').toArray();
                for (let name of names) {
                            let forname = $(name).attr('name');
                            let typename = $(name).attr('type');

                            let adicionarCremento = typename == 'file' ? forname : forname.replace(/\[\d*\]/, '[' + i + ']');

                            //verificar se é do tipo file
                            const fname = adicionarCremento ;
                            $(name).attr('name', fname);
                        }
                
                // LÃ³gica dos botÃµes
                const multiBtns = $(row).find('.multiBtns');
                if (!multiBtns.length) {
                    $(row).append('<div class="multiBtns"></div>');
                }

                $(row).find('.addMulti').remove();
                $(row).find('.removeMulti').remove();

                if (i == 1) {
                    $(row).find('.multiBtns').append(`<button type="button" class="addMulti btn btn-success">Adicionar <i class="fa fa-plus-circle" aria-hidden="true"></i></button>`);
                } else {
                    $(row).find('.multiBtns').append(`<button type="button" class="addMulti btn btn-success">Adicionar <i class="fa fa-plus-circle" aria-hidden="true"></i></button>`);
                    $(row).find('.multiBtns').append(`<button type="button" class="btn btn-danger removeMulti">Remover <i class="fa fa-minus-circle" aria-hidden="true"></i></button>`);
                }

                i++;
            }
        }
    }

    //addmulti
    $(document).on('click', '.multi .addMulti', function() {
        let groupClass = $(this).parents('.multi').attr('class');
        if(groupClass != undefined){
            groupClass = groupClass.split(' ')[1]

            let clone = $(`.multi.${groupClass}`).last().clone();

            //removendo dados desnecessários
            clone.find('.uploadInput').remove()
            clone.find('.fileAnexo').css({'display':'block'}) //caso esteja invisivel
            clone.find('input').val('')


            $(`.multi.${groupClass}`).last().after(clone.find('input:text').val('').end());
            gerarMulti('.aw-form-row');
        }
        
    });

    $(document).on('click', '.multi .removeMulti', function() {
        $(this).parents('.multi').remove();
        gerarMulti('.aw-form-row');
    });

    // Chame criarGrupos() e, em seguida, chame gerarMulti() quando a Promise for resolvida
    criarGrupos().then(function() {
        gerarMulti('.aw-form-row');
    });
});