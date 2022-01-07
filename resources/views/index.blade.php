@extends('layouts.main')
@section('title')
    Home
@endsection
@section('content')
    <div class="title row">
        <div class="col-md-12">
            <h1 style="font-size: 4em; color:#ff5e7e">todos</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 list">
            <div class="row">
                <div class="col-md-12 input-group">
                    <label class="input-group-text" style="cursor:pointer; border-radius: 0px" onclick="checkAll()">
                        <i class="fas fa-chevron-down"></i>
                    </label>
                    <input class="form-control" style="outline:none;" type="text" name="" id="add-value" value="" />
                </div>
            </div>
            <ul class="list-results">

            </ul>
            <div class="col-md-12 footer">
                <div class="row">
                    <div class="col-md-3 pt-2">
                        <div class="itens-left">
                            <label></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-filters">
                            <li class="selected" data-filtro="0">All</li>
                            <li data-filtro="1">Active</li>
                            <li data-filtro="2">Completed</li>
                        <ul>
                    </div>
                    <div class="col-md-3 pt-2">
                        <label id="delete-completes" onclick="deleteCompletes()">Clear completed</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('onPageScript')
<script type="text/javascript">
    var check = true;
    function printElemento(valor, id, status) {
        var place_valor = valor;
        if (status==2) {
            place_valor = `<del style="color:#d3d3d3">${valor}</del>`;
        }
        let elemento = `
                    <li>
                        <div class='row' style='margin:0px'>
                            <div class='col-md-1' style='padding:0px'>
                                <button id='mark-resolved' data-resolved='${ status==1 ? false : true }' data-valor='${valor}' data-id='${id}' data-status='${status}'>
                                    ${
                                        (status==1) ? '' : "<i class='fas fa-check' style='color:#44bd64;'></i>"
                                    }
                                </button>
                            </div>
                            <div class='col-md-11'>
                                <label id='valor' data-status='${status}' data-id='${id}'>
                                    ${place_valor}
                                </label>
                            </div>
                            <button class='btn' style='background-color:#ff5e7e; color:white;' id='bt-delete' data-id='${id}' data-status='${status}'>
                                <i class='fas fa-trash'></i>
                            </button>
                        </div>
                    </li>
                `;
        $(".list-results").append(elemento);
    }

    function countRest(status){
        if (status) {
            $.post("{{route('registros.count')}}", { _token: "{{ csrf_token() }}", status: status}, function(response){
                $(".itens-left label").html(`${response} itens restantes`);
            });
        } else {
            $.post("{{route('registros.count')}}", { _token: "{{ csrf_token() }}"}, function(response){
                $(".itens-left label").html(`${response} itens restantes`);
            });
        }
    }

    function loadAllRegistros() {
        $.post("{{route('registros.all')}}", { _token: "{{ csrf_token() }}"}, function(response){
            $(".list-results").html("");
            response.forEach(element => {
                printElemento(element.valor, element.id, element.status);
            });
            countRest();
        });
    }

    function editRegistro(id_registro, valor_registro, status_registro){
        $.post(
            "{{route('registros.edit')}}", 
            { 
                _token: "{{ csrf_token() }}", 
                id: id_registro,
                valor: valor_registro,
                status: status_registro,
            }, 
            function(response){
                filter($(".list-filters li.selected").attr('data-filtro'));
            }
        );
    }

    function deleteRegistro(id_registro) {
        $.post("{{route('registros.delete')}}", { _token: "{{ csrf_token() }}", id: id_registro}, function(response){
            if (response) {
                filter($(".list-filters li.selected").attr('data-filtro'));
                countRest();
            }
        });
    }

    function addRegistro(value){
        $.post("{{route('registros.add')}}", { _token: "{{ csrf_token() }}", value: value}, function(response){
            if (response) {
                filter($(".list-filters li.selected").attr('data-filtro'));
                countRest();
            }
        });
    }

    function filter(id_filtro) {
        if (id_filtro==0) {
            $.post("{{route('registros.filter')}}", { _token: "{{ csrf_token() }}" }, function(response){
                $(".list-results").html("");
                response.forEach(element => {
                    printElemento(element.valor, element.id, element.status);
                });
                countRest();
            });
        } else {
            $.post("{{route('registros.filter')}}", { _token: "{{ csrf_token() }}", status_id: id_filtro}, function(response){
                $(".list-results").html("");
                response.forEach(element => {
                    printElemento(element.valor, element.id, element.status);
                });
                countRest();
            });
        }
    }

    function checkAll() {
        check = !check;
        $.post("{{route('registros.checkall')}}", { _token: "{{ csrf_token() }}", status: check}, function(response){
            filter($(".list-filters li.selected").attr('data-filtro'));
        });
    }

    function deleteCompletes(){
        $.post("{{route('registros.deletecompleted')}}", { _token: "{{ csrf_token() }}"}, function(response){
            console.log(response);
            filter($(".list-filters li.selected").attr('data-filtro'));
        });
    }

    $(function(){
        loadAllRegistros();

        $(".list-filters li").click(function(){
            $(this).toggleClass("selected");
            $(this).siblings("li").removeClass("selected");
            var filtro = $(this).attr('data-filtro');
            if (filtro==0) {
                loadAllRegistros();
            } else {
                filter(filtro);
            }
        });

        $(document).on('click', '#bt-delete', function(){
            deleteRegistro($(this).attr('data-id'));
        });

        $(document).on("keypress", function(e){
            if (e.keyCode==13) {
                if ($("#add-value").is(":focus")) {
                    var value = $("#add-value").val();
                    addRegistro(value);
                    $("#add-value").val("");
                }
            }
        });
        
        $(document).on("dblclick", "label#valor", function(){
            $(this).attr("contentEditable", "true");
        });

        $(document).on("blur", "label#valor", function(){
            var value = $(this).html();
            editRegistro($(this).attr('data-id'), value, $(this).attr('data-status'))
            $(this).attr("contentEditable", "false");
        });

        $(document).on("click", "#mark-resolved", function(){
            if ($(this).attr('data-resolved')=='true'){
                $(this).attr('data-resolved', 'false');
                editRegistro($(this).attr('data-id'), $(this).attr('data-valor'), 1);
            } else {
                $(this).attr('data-resolved', 'true');
                editRegistro($(this).attr('data-id'), $(this).attr('data-valor'), 2);
            }
        });

    });
</script>
@endsection