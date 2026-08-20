@extends('admin_layout.master')
@section('content')
<div class="nk-content">
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title">Footer Section</h4>
        </div>
    </div>
    <div class="container-fluid">
        <form action="{{ url('/admin-dashboard/add/footer') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="card card-bordered card-preview">
                <div class="card-inner">

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="beginning_of_footer">Code at Beginning of  <<span>footer</span>> Tag</label>

                            <textarea  class="form-control" id="beginning_of_footer" name="beginning_of_footer" cols="15" rows="7">{{$data['beginning_of_footer'] ?? ''}}</textarea>
                        </div>
                    </div>
                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="end_of_footer">Code before Closing <<span>/footer</span>> Tag
                            </label>

                            <textarea  class="form-control" id="end_of_footer" name="end_of_footer" cols="15" rows="7">{{$data['end_of_footer'] ?? ''}}</textarea>
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="footer_copyright">Footer copyright text</label>
                            <input type="text" class="form-control" id="footer_copyright" name="footer_copyright" value="{{ $data['footer_copyright'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="footer_text">Footer  text</label>
                            <input type="text" class="form-control" id="footer_text" name="footer_text" value="{{ $data['footer_text'] ?? '' }}">
                        </div>
                    </div>


                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                             <label class="form-label" for="footer_logo">Footer Logo</label>
                             <input type="file" class="form-control" id="footer_logo" name="footer_logo">
                        </div>
                        @if(isset($data['footer_logo']) && $data['footer_logo'] != null)
                             <div class="footer_image_div" id="footer_image_div{{ $data['footer_logo_id'] ?? '' }}">
                                  <div class="form-group">
                                       <span class="col-md-8 offset-md-4 remove_footer_logo" data-id="{{ $data['footer_logo_id'] ?? '' }}">
                                            <i class="fa fa-times"></i>
                                       </span>
                                  </div>
                                  <div class="form-group">
                                       <img src="{{ asset('storage/'.$data['footer_logo'] ?? '' ) }}" style="height:150px;width:150px">
                                  </div>
                             </div>
                        @endif
                   </div>
                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="documentos_footer">Documentos footer</label>
                            <input type="text" class="form-control" id="documentos_footer" name="documentos_footer" value="{{ $data['documentos_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="negocios_y_comercio_footer">Negocios y Comercio footer</label>
                            <input type="text" class="form-control" id="negocios_y_comercio_footer" name="negocios_y_comercio_footer" value="{{ $data['negocios_y_comercio_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="vida_personal_footer">Vida Personal footer</label>
                            <input type="text" class="form-control" id="vida_personal_footer" name="vida_personal_footer" value="{{ $data['vida_personal_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="laboral_y_cumplimiento_footer">Laboral y Cumplimiento footer</label>
                            <input type="text" class="form-control" id="laboral_y_cumplimiento_footer" name="laboral_y_cumplimiento_footer" value="{{ $data['laboral_y_cumplimiento_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="tecnologia_y_consumo_footer">Tecnología y Consumo footer</label>
                            <input type="text" class="form-control" id="tecnologia_y_consumo_footer" name="tecnologia_y_consumo_footer" value="{{ $data['tecnologia_y_consumo_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="informacion_footer">Información footer</label>
                            <input type="text" class="form-control" id="informacion_footer" name="informacion_footer" value="{{ $data['informacion_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="sobre_nosotros_footer">Sobre nosotros footer</label>
                            <input type="text" class="form-control" id="sobre_nosotros_footer" name="sobre_nosotros_footer" value="{{ $data['sobre_nosotros_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="precios_footer">Precios footer</label>
                            <input type="text" class="form-control" id="precios_footer" name="precios_footer" value="{{ $data['precios_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="contacto_footer">Contacto footer</label>
                            <input type="text" class="form-control" id="contacto_footer" name="contacto_footer" value="{{ $data['contacto_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="facturacion_footer">Facturación footer</label>
                            <input type="text" class="form-control" id="facturacion_footer" name="facturacion_footer" value="{{ $data['facturacion_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="ayuda_footer">Ayuda footer</label>
                            <input type="text" class="form-control" id="ayuda_footer" name="ayuda_footer" value="{{ $data['ayuda_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="centro_de_ayuda_footer">Centro de Ayuda footer</label>
                            <input type="text" class="form-control" id="centro_de_ayuda_footer" name="centro_de_ayuda_footer" value="{{ $data['centro_de_ayuda_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="asi_funciona_footer">Así funciona footer</label>
                            <input type="text" class="form-control" id="asi_funciona_footer" name="asi_funciona_footer" value="{{ $data['asi_funciona_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="preguntas_frecuentes_footer">Preguntas Frecuentes footer</label>
                            <input type="text" class="form-control" id="preguntas_frecuentes_footer" name="preguntas_frecuentes_footer" value="{{ $data['preguntas_frecuentes_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="legal_footer">Legal footer</label>
                            <input type="text" class="form-control" id="legal_footer" name="legal_footer" value="{{ $data['legal_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="terminos_y_condiciones_footer">Términos y Condiciones footer</label>
                            <input type="text" class="form-control" id="terminos_y_condiciones_footer" name="terminos_y_condiciones_footer" value="{{ $data['terminos_y_condiciones_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="aviso_de_privacidad_footer">Aviso de Privacidad footer</label>
                            <input type="text" class="form-control" id="aviso_de_privacidad_footer" name="aviso_de_privacidad_footer" value="{{ $data['aviso_de_privacidad_footer'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="aviso_legal_footer">Aviso Legal footer</label>
                            <input type="text" class="form-control" id="aviso_legal_footer" name="aviso_legal_footer" value="{{ $data['aviso_legal_footer'] ?? '' }}">
                        </div>
                    </div>



                </div>




            </div>
            <div class="mt-3">
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<script>

    $(document).ready(function(){
        $('.remove_header_logo').click(function(){
            id = $(this).data('id');
            // $('#remove_logo1').val(id);
            $('#header_image_div'+id).hide();
        });

        $('.remove_footer_logo').click(function(){
            id = $(this).data('id');
            // $('#remove_logo2').val(id);
            $('#footer_image_div'+id).hide();
        });

        $('.remove_favicon').click(function(){
            id = $(this).data('id');
            // $('#favicon_img_id').val(id);
            $('#favicon_image_div'+id).hide();
        });
    });

    </script>

@endsection
