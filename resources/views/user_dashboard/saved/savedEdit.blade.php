@extends('user_dashboard_layout.master')
@section('content')
    <h2 class="m-0">Editar Documento
    </h2>
    <div class="wrap_size">

        <div class="wrap">
            <div class="toolbar">
                <button id="bold" title="Bold (Ctrl+B)"><i class="fa fa-bold"></i></button>
                <button id="italic" title="Italic (Ctrl+I)"><i class="fa fa-italic"></i></button>


                <button id="align-left" title="Left"><i class="fa fa-align-left"></i></button>
                <button id="align-center" title="Center"><i class="fa fa-align-center"></i></button>
                <button id="align-right" title="Right"><i class="fa fa-align-right"></i></button>

            </div>
            <div class="editor" contenteditable>

                <h2>Editar Documento</h2>
                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Sequi natus repellat veniam
                    magni aperiam dolorum eaque assumenda hic sint quos, officia, consequuntur iusto
                    quaerat fuga ducimus aliquid illum vitae ipsam?</p>


                <h4>Editar Document</h4>

                <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Necessitatibus totam illum
                    recusandae, aliquam repudiandae, tempora aperiam corrupti nesciunt quae, numquam
                    excepturi iste quam qui voluptas beatae facere ratione culpa. Voluptates!</p>

                <h5>Editar Documento</h5>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatibus itaque eligendi
                    nihil praesentium consequatur cupiditate quasi, quam quas id placeat asperiores
                    recusandae quod! Recusandae ullam nisi quo est, nam impedit!</p>
            </div>
        </div>
    </div>
    <div class="editar_documento">
        <div class="dcmnt_btn">
            <a href="#" class=" unq_btn trpt_bg_btn">Cancelar</a>
        </div>
        <div class="dcmnt_btn">
        <a href="#" class=" unq_btn "> Guardar</a>
    </div>
    
    </div>
@endsection