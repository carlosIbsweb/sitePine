jQuery(function($) {

    function geraMulti(sel) {
        // Envolvendo o elemento pai de cada gMultiRows com base no data-group
        $('.aw-form-row[data-group]').each(function() {
            const groupValue = $(this).attr('data-group');
            const parentRow = $(this).closest('.aw-form-row');
            if (!parentRow.parent().hasClass(groupValue)) {
                parentRow.wrap(`<div class="multi ${groupValue}"></div>`);
            }
        });

        // Restante do código permanece o mesmo
        const distinctGroups = [...new Set($('.multi').map(function() {
            return $(this).attr('data-group');
        }).get())];

        for (const group of distinctGroups) {
            let rows = $(`.multi.${group}`).toArray();
            let i = 1;
            for (let row of rows) {
                let names = $(row).find('[name]').toArray();
                for (let name of names) {
                    let forname = $(name).attr('name');
                    const fname = forname.replace(/\[\d*\]/, '[' + i + ']');
                    $(name).attr('name', fname);
                }

                // Lógica dos botões
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

    $(document).on('click', '.multi .addMulti', function() {
        const groupClass = $(this).parents('.multi').attr('class').split(' ')[1];
        const clone = $(`.multi.${groupClass}`).last().clone();
        $(`.multi.${groupClass}`).last().after(clone.find('input:text').val('').end());
        geraMulti('.aw-form-row');
    });

    $(document).on('click', '.multi .removeMulti', function() {
        $(this).parents('.multi').remove();
        geraMulti('.aw-form-row');
    });

    geraMulti('.aw-form-row');
});
