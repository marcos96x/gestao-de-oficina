var vm = new Vue({
    el: '#app',
    data: {
        categorias: null,
        pecas: null,
        rm: null,
    },
    methods: {
        listar: function () {
            let data = {
                'action': 'getPecas',
            };
            $.ajax({
                url: __BASE_API__,
                type: 'POST',
                data: data
            }).done(res => {
                
                res = JSON.parse(res);

                if (res.status == undefined) {
                    console.log('erro na requisição');
                    return false;
                }

                if (res.status == 200) {
                    vm.pecas = res.data;
                } else {
                    vm.pecas = null;
                }
            })
        },
        listar_categorias: function () {
            let data = {
                'action': 'getCategorias',
            };
            $.ajax({
                url: __BASE_API__,
                type: 'POST',
                data: data
            }).done(res => {
                res = JSON.parse(res);

                if (res.status == undefined) {
                    console.log('erro na requisição');
                    return false;
                }

                if (res.status == 200) {
                    vm.categorias = res.data;
                } else {
                    vm.categorias = null;
                }
            })
        },
        gravar: function () {
            let nome = $("#nomeNovo").val();
            let categoria = $("#categoriaNovo").val();
            let referencia = $("#referenciaNovo").val();
            let estoqueMin = $("#estoqueMinNovo").val();
            let estoqueMax = $("#estoqueMaxNovo").val();

            let data = {
                'action': 'salvaPeca',
                'nome': nome,
                'tipo': categoria,
                'referencia': referencia,
                'estoque_qtd_min': estoqueMin,
                'estoque_qtd_atual': estoqueMax,
            };
            $.ajax({
                url: __BASE_API__,
                type: 'POST',
                data: data
            }).done(res => {
                
                res = JSON.parse(res);

                if (res.status == undefined) {
                    console.log('erro na requisição');
                    return false;
                }

                $("#nomeNovo").val('');
                $("#categoriaNovo").val('0').trigger('change');
                $("#referenciaNovo").val('');
                $("#estoqueMinNovo").val('');
                $("#estoqueMaxNovo").val('');
                $("#modalCadastro").modal('hide');
                vm.listar();
            })
        },
        show_edit: function (peca) {
            $("#nomeEdit").val(peca.peca_nome);
            $("#categoriaEdit").val(peca.peca_tipo).trigger('change');
            $("#referenciaEdit").val(peca.peca_referencia);
            $("#estoqueMinEdit").val(peca.peca_estoque_qtd_min);
            $("#estoqueAtualEdit").val(peca.peca_estoque_qtd_atual);
            $("#idEdit").val(peca.peca_id);
            $("#modalEdit").modal('show');
        },
        editar: function () {
            let nome = $("#nomeEdit").val();
            let categoria = $("#categoriaEdit").val();
            let referencia = $("#referenciaEdit").val();
            let estoqueMin = $("#estoqueMinEdit").val();
            let estoqueAtualEdit = $("#estoqueAtualEdit").val();
            let id = $("#idEdit").val();

            if (nome.trim == "" && Number(id) == 0) {
                $("#nomeEdit").val();
                return false;
            }

            let data = {
                'action': 'editaPeca',
                'nome': nome,
                'estoque_qtd_min': estoqueMin,
                'referencia': referencia,
                'tipo': categoria,
                'estoque_qtd_atual': estoqueAtualEdit,
                'id': id,
            };
            $.ajax({
                url: __BASE_API__,
                type: 'POST',
                data: data
            }).done(res => {
                
                res = JSON.parse(res);

                if (res.status == undefined) {
                    console.log('erro na requisição');
                    return false;
                }

                $("#nomeEdit").val('');
                $("#categoriaEdit").val('');
                $("#referenciaEdit").val('');
                $("#estoqueMinEdit").val('');
                $("#estoqueAtualEdit").val('');
                $("#idEdit").val('');
                $("#modalEdit").modal('hide');
                vm.listar();
            })
        },
        show_remove: function (cat) {
            vm.rm = cat;
            $("#modalRemove").modal('show');
        },
        remove: function () {
            if (vm.rm != null) {
                let data = {
                    'action': 'removePeca',
                    'id': vm.rm.peca_id,
                };
                $.ajax({
                    url: __BASE_API__,
                    type: 'POST',
                    data: data
                }).done(res => {
                    
                    res = JSON.parse(res);

                    if (res.status == undefined) {
                        console.log('erro na requisição');
                        return false;
                    }

                    $("#modalRemove").modal('hide');
                    vm.listar();
                })
            }
        }
    },
    // LifeCicle
    created: function () {
        this.listar();
        this.listar_categorias();
    }
})