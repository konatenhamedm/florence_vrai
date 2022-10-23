$(function () {
    init_select2(null, null, '.form-card');


    $('.no-auto').each(function () {
        const $this = $(this);
        const $id = $('#' + $this.attr('id'));
        init_date_picker($id,  'down', (start, e) => {
            //$this.val(start.format('DD/MM/YYYY'));
        }, null, null, false);

        $id.on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY'));
        });
    });
    const $container_doc = $('.doc-list');
    const $container_wk = $('.workflow-list');
    var index_doc = $container_doc.find('.row-line').length;
    var index_wk = $container_wk.find('.row-line').length

    const $addLink = $('.add_line');
    $addLink.click(function(e) {
        const $this  = $(this);
        const proto_class = $this.attr('data-prototype');
        const name = $this.attr('data-protoname');
        const $container = $($this.attr('data-container'));
        let max_etape = +$container.find('.row-line').last().find('.numero-etape').val();
        if (isNaN(max_etape)) {
            max_etape = 0;
        }

        addLine($container, name, proto_class, max_etape);

        //addDeleteLink($container);

        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
    });
    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un (cas d'une nouvelle annonce par exemple).
    /*if (index == 0) {
    addimputation($container);
    } else {*/
    if (index_doc > 0) {
        $container_doc.children('.row-line').each(function() {
            const $this = $(this);
            addDeleteLink($this);
            $this.find("select").each(function() {
                const $this = $(this);
                init_select2($this, null, '.form-card');
            });




        });


    }

    if (index_wk > 0) {
        $container_wk.children('.row-line').each(function() {
            const $this = $(this);
            addDeleteLink($this);
        });
    }


    // La fonction qui ajoute un formulaire Categorie
    function addLine($container, name, proto_class, max_etape = null) {
        // Dans le contenu de l'attribut « data-prototype », on remplace :
        // - le texte "__name__label__" qu'il contient par le label du champ
        // - le texte "__name__" qu'il contient par le numéro du champ

        var $prototype = $($(proto_class).attr('data-prototype')
            .replace(new RegExp(name + 'label__', 'g'), 'Pièce ' + (name == '__workflow__' ? index_wk + 1 : index_doc + 1))
            .replace(new RegExp(name, 'g'), name == '__workflow__' ? index_wk : index_doc));


        // On ajoute au prototype un lien pour pouvoir supprimer la prestation
        addDeleteLink($prototype, name);
        // On ajoute le prototype modifié à la fin de la balise <div>
        $container.append($prototype);




        if (name == '__workflow__') {
            index_wk++;
            $prototype.find('.numero-etape').val(max_etape + 1).attr('data-etape', max_etape + 1);
        } else {
            index_doc++;
        }

    }


    function addDeleteLink($prototype, name = null) {
        // Création du lien
        $deleteLink = $('<a href="#" class="btn btn-danger"><span class="fa fa-trash"></span></a>');
        // Ajout du lien
        $prototype.find(".del-col").append($deleteLink);



        // Ajout du listener sur le clic du lien
        $deleteLink.click(function(e) {
            const $parent = $(this).closest('.row-line');
            $parent.remove();

            if (name == '__document__') {
                if (index_doc > 0) {
                    index_doc -= 1;
                }
            } else if (name == '__workflow__') {
                console.log( index_wk );
                if (index_wk > 0) {
                    index_wk -= 1;
                    $('.numero-etape').each(function (index, c) {

                        $(this).val(index + 1);
                    });
                }
            }
            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        });
    }
});

/*

$(document).ready(function () {
//alert("vff")
    var $collectionHolder;
    // setup an "add a tag" link
    var $addTagButton = $('.add_groupe');


    /!*var $after = $('tr');*!/
    /!*var $newLinkLi = $('<li></li>').append($addTagButton);*!/
    /!*    addForm();*!/
    $(document).ready(function () {
        $collectionHolder = $('#groupe');
        /!*$collectionHolder.append($addTagButton);*!/
        $collectionHolder.data('index', $collectionHolder.find('.container').length)
        $collectionHolder.find('.container').each(function () {
            addRemoveButton($(this));
        })
        $addTagButton.click(function (e) {

            //alert("jhghghg")
            e.preventDefault();
            addForm();
            refresh();
            // $('select').select2();
        })

    })

    function refresh() {
        let index = 0
        $('.ligne').each(function () {
            index++;
            $(this).attr('data-numberKey', index)
            $(this).find('.numero:first').val(index);
        })
    }

    function addForm() {
        var prototype = $collectionHolder.data('prototype');
        var index = $collectionHolder.data('index');
        var newForm = prototype;
        newForm = newForm.replace(/__name__/g, index);
        $collectionHolder.data('index', index + 1);

        var $card = $('<span class="container col-md-12 "></span>');
        $card.append(newForm);

        addRemoveButton($card);
        $collectionHolder.children("input[type='hidden']:first").before($card)
        $collectionHolder.find('.after').before($card);

    }

    function addRemoveButton($card) {

        var $removeButton = $('<a href="#" class="btn btn-danger supprimer" style="margin-left: -16px" data-card-tool="remove" data-toggle="tooltip"\n' +
            '           data-placement="top" title="" data-original-title="Remove Card"><i class="fe fe-trash-2 icon-nm"></i> </a>');
        /!*var $cardFooter = $('<div class="modal-footer"></div>').append($removeButton);*!/

        $removeButton.click(function (e) {
            console.log($(e.target).parent('.container'));

            $(e.target).parents('.container').slideUp(1000, function () {
                $(this).remove();
                refresh();
            });

        })

        $card.find(".supprimer").append($removeButton);
    }
    // $card.find(".supprimer").append($removeButton);

});*/
