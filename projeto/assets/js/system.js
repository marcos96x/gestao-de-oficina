function loginAdmin() {
    let login = $("#login").val(); // capturando o valor de um input cujo ID seja login
    let senha = $("#senha").val(); // capturando o valor de um input cujo ID seja senha

    // usando o .trim() depois de uma string pra remover espaços desnecessários
    if (login.trim() == '' || senha.trim() == '') {
        alert("Login e senha obrigatoŕio");
        return false;
    }

    // campos preenchidos, realiza a requisição para a API
    // requisição POST simples, envia dados pra API e retorna o status
    let data = {
        'action': 'loginAdmin', // action = rota da api
        // abaixo do action, todos os dados que a api precisa
        'login': login,
        'senha': senha
    };
    $.ajax({
        url: __BASE_API__, // constante definida no arquivo main.js
        type: 'POST', // verbo da requisição, todas da API são POST
        data: data // body da requisição
    }).done(res => {
        // dentro dessa variavel "res" está o resultado da request
        res = JSON.parse(res); // convertendo o resultado pra JSON

        // toda request retorna um status, se não retornar, aconteceu algum problema com a request
        if (res.status == undefined) {
            console.log('erro na requisição');
            return false;
        }

        if (res.status == 200) {
            // sucesso
            // no caso do login, e apenas no login, ele faz a autenticação na api, guardando na SESSION do servidor que vc ta logado
            // portanto, caso no login retorne 200, redirect pro dashboard
            window.location.href = __BASE_URI__ + "/pages/dashboard/index.html";
        } else {
            // erro
            console.log("Erro com sua requisição")
            console.log(res);
            alert(res.msg)
        }
    })
}

function logout() {
    // logout é um exemplo de request que n retorna nada e nem envia nada, só bate na rota de logout, quebrando a SESSION que foi guardada ao logar
    let data = {
        'action': 'logoutAdmin',
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
            window.location.href = __BASE_URI__ + "/index.html";
        }
    })
}

function autenticacao() {
    let data = {
        action: 'autenticacao'        
    };
    $.ajax({
        url: __BASE_API__,
        type: 'POST',
        data: data
    }).done(res => {
        console.log(res)
        res = JSON.parse(res);

        if(res.status == undefined) {
            console.log('erro na requisição');
            return false;
        }

        if(res.status == 401) {
            window.location.href = __BASE_URI__ + "/index.html?autenticacao=false" ;
        } 
    })
}