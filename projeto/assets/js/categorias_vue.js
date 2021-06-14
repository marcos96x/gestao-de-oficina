var vm = new Vue({
    el: '#app',
    data: {
        categorias: null,
        rm: null,
    },
    methods: {
        listar: function () {
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



            let data = {
                'action': 'salvaCategoria',
                'nome': nome,
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

                $("#modalCadastro").modal('hide');
                vm.listar();
            })
        },
        show_edit: function (categoria) {
            $("#nomeEdit").val(categoria.categoria_peca_nome);
            $("#idEdit").val(categoria.categoria_peca_id);
            $("#modalEdit").modal('show');
        },
        editar: function () {
            let nome = $("#nomeEdit").val();
            let id = $("#idEdit").val();

            let data = {
                'action': 'editaCategoria',
                'nome': nome,
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
                    'action': 'removeCategoria',
                    'id': vm.rm.categoria_peca_id,
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
    }
})