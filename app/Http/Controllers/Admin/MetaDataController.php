<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MetaData;


class MetaDataController extends Controller
{
    //
    public function header(){

        $keys=[
            'begin_of_head',
            'end_of_head',
            'header_btn_1',
            'header_btn_2',
            'header_logo',
            'user_dash_header_logo',
            'favicon',
            'asi_funciona_header',
            'ayuda_header',
            'header_search_placeholder',
            'header_document_search_placeholder',
            'header_document_search_message',
            'mobile_header_logo'
        ];

        $result=MetaData::WhereIn('key',$keys)->get()->keyBy('key');


        $data=[
            'begin_of_head'=>$result['begin_of_head']->value ?? null,
            'end_of_head'=>$result['end_of_head']->value ?? null ,
            'button1' => $result['header_btn_1']->value ?? null,
            'button2' => $result['header_btn_2']->value ?? null,
            'asi_funciona_header' => $result['asi_funciona_header']->value ?? null,
             'ayuda_header' => $result['ayuda_header']->value ?? null,

            'header_logo_id' => $result['header_logo']->id ?? null,
            'header_logo' => str_replace('public/', '', $result['header_logo']->file_path ?? null),

            'user_dash_header_logo_id' => $result['user_dash_header_logo']->id ?? null,
            'user_dash_header_logo'=>str_replace('public/', '', $result['user_dash_header_logo']->file_path ?? null),

            'favicon_id' => $result['favicon']->id ?? null,
            'favicon' => str_replace('public/', '', $result['favicon']->file_path ?? null),

            'header_search_placeholder'=>$result['header_search_placeholder']->value ?? null,
            'header_document_search_placeholder'=>$result['header_document_search_placeholder']->value ?? null ,
            'header_document_search_message'=>$result['header_document_search_message']->value ?? null ,

            'mobile_header_logo' => str_replace('storage/logos/', '', $result['mobile_header_logo']->file_path ?? null),

        ];
        // dd($data);

        return view('admin.globals.header',compact('data'));
    }

    public function addHeader(Request $request){
        try{
            if($request->hasFile('header_logo')){
                $file = $request->file('header_logo');
                $directory = "public/logos";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $web_setting = MetaData::where('key','header_logo')->first();
                $web_setting->value = $filename;
                $web_setting->file_path = $filepath;
                $web_setting->update();
            }

            if($request->hasFile('user_dash_header_logo')){
                $file = $request->file('user_dash_header_logo');
                $directory = "public/logos";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $web_setting = MetaData::where('key','user_dash_header_logo')->first();
                $web_setting->value = $filename;
                $web_setting->file_path = $filepath;
                $web_setting->update();
            }

            if($request->hasFile('mobile_header_logo')){
                $file = $request->file('mobile_header_logo');
                $directory = "storage/logos";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);
            
                $web_setting = MetaData::where('key','mobile_header_logo')->first();
                $web_setting->value = $filename;
                $web_setting->file_path = $filepath;
                $web_setting->update();
            }

            if($request->hasFile('favicon')){
                $file = $request->file('favicon');
                $directory = "public/logos";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $web_setting = MetaData::where('key','favicon')->first();
                $web_setting->value = $filename;
                $web_setting->file_path = $filepath;
                $web_setting->update();
            }

            $fields = [
                'begin_of_head'=> 'begin_of_head',
                'end_of_head'=>'end_of_head',
                'header_btn_1' => 'header_btn_1',
                'header_btn_2' => 'header_btn_2',
                'ayuda_header' => 'ayuda_header',
                'asi_funciona_header' => 'asi_funciona_header',
                'header_document_search_placeholder'=> 'header_document_search_placeholder',
                'header_search_placeholder'=>'header_search_placeholder',
                'header_document_search_message'=>'header_document_search_message'

            ];

            foreach($fields as $key=>$input){
                if($request->has($input)) {
                    $web_setting = MetaData::where('key', $key)->first();
                    if($web_setting){
                        $web_setting->value = $request->$input;
                        $web_setting->update();
                    }
                }
            }
            return redirect()->back()->with('success', 'Data successfully saved.');
        }
        catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function footer(){
        $key=[
            'beginning_of_footer',
            'end_of_footer',
            'footer_copyright',
            'footer_text',
            'footer_logo',
            'documentos_footer',
            'negocios_y_comercio_footer',
            'vida_personal_footer',
            'laboral_y_cumplimiento_footer',
            'tecnologia_y_consumo_footer',
            'informacion_footer',
            'sobre_nosotros_footer',
            'precios_footer',
            'contacto_footer',
            'facturacion_footer',
            'ayuda_footer',
            'centro_de_ayuda_footer',
            'asi_funciona_footer',
            'preguntas_frecuentes_footer',
            'legal_footer',
            'terminos_y_condiciones_footer',
            'aviso_de_privacidad_footer',
            'aviso_legal_footer',


        ];

        $result=MetaData::WhereIn('key',$key)->get()->keyBy('key');

        $data=[
            'beginning_of_footer'=>$result['beginning_of_footer']->value ?? null,
            'end_of_footer'=>$result['end_of_footer']->value ?? null,

            'footer_copyright'=>$result['footer_copyright']->value ?? null,
            'footer_text'=>$result['footer_text']->value ?? null,

            'footer_logo_id' => $result['footer_logo']->id ?? null,
            'footer_logo' => str_replace('public/', '', $result['footer_logo']->file_path ?? null),
            'documentos_footer' => $result['documentos_footer']->value ?? null,
            'negocios_y_comercio_footer' => $result['negocios_y_comercio_footer']->value ?? null,
            'vida_personal_footer' => $result['vida_personal_footer']->value ?? null,
            'laboral_y_cumplimiento_footer' => $result['laboral_y_cumplimiento_footer']->value ?? null,
            'tecnologia_y_consumo_footer' => $result['tecnologia_y_consumo_footer']->value ?? null,
            'informacion_footer' => $result['informacion_footer']->value ?? null,
            'sobre_nosotros_footer' => $result['sobre_nosotros_footer']->value ?? null,
            'precios_footer' => $result['precios_footer']->value ?? null,
            'contacto_footer' => $result['contacto_footer']->value ?? null,
            'facturacion_footer' => $result['facturacion_footer']->value ?? null,
            'ayuda_footer' => $result['ayuda_footer']->value ?? null,
            'centro_de_ayuda_footer' => $result['centro_de_ayuda_footer']->value ?? null,
            'asi_funciona_footer' => $result['asi_funciona_footer']->value ?? null,
            'preguntas_frecuentes_footer' => $result['preguntas_frecuentes_footer']->value ?? null,
            'legal_footer' => $result['legal_footer']->value ?? null,
            'terminos_y_condiciones_footer' => $result['terminos_y_condiciones_footer']->value ?? null,
            'aviso_de_privacidad_footer' => $result['aviso_de_privacidad_footer']->value ?? null,
            'aviso_legal_footer' => $result['aviso_legal_footer']->value ?? null,

        ];
        //   dd($data);

        return view('admin.globals.footer',compact('data'));
    }


    public function addFooter(Request $request){
        try{

            if($request->hasFile('footer_logo')){
                $file = $request->file('footer_logo');
                $directory = "public/logos";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $web_setting = MetaData::where('key','footer_logo')->first();
                $web_setting->value = $filename;
                $web_setting->file_path = $filepath;
                $web_setting->update();
            }

            $fields = [
                'beginning_of_footer' => 'beginning_of_footer',
                'end_of_footer' => 'end_of_footer',
                'footer_text'=>'footer_text',
                'footer_copyright' => 'footer_copyright',
                'documentos_footer' => 'documentos_footer',
                'negocios_y_comercio_footer' => 'negocios_y_comercio_footer',
                'vida_personal_footer' => 'vida_personal_footer',
                'laboral_y_cumplimiento_footer' => 'laboral_y_cumplimiento_footer',
                'tecnologia_y_consumo_footer' => 'tecnologia_y_consumo_footer',
                'informacion_footer' => 'informacion_footer',
                'sobre_nosotros_footer' => 'sobre_nosotros_footer',
                'precios_footer' => 'precios_footer',
                'contacto_footer' => 'contacto_footer',
                'facturacion_footer' => 'facturacion_footer',
                'ayuda_footer' => 'ayuda_footer',
                'centro_de_ayuda_footer' => 'centro_de_ayuda_footer',
                'asi_funciona_footer' => 'asi_funciona_footer',
                'preguntas_frecuentes_footer' => 'preguntas_frecuentes_footer',
                'legal_footer' => 'legal_footer',
                'terminos_y_condiciones_footer' => 'terminos_y_condiciones_footer',
                'aviso_de_privacidad_footer' => 'aviso_de_privacidad_footer',
                'aviso_legal_footer' => 'aviso_legal_footer',

            ];

            foreach($fields as $key=>$input){
                if($request->has($input)) {
                    $web_setting = MetaData::where('key', $key)->first();
                    if($web_setting){
                        $web_setting->value = $request->$input;
                        $web_setting->update();
                    }
                }
            }

            return redirect()->back()->with('success', 'Data successfully saved.');



        }


        catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }

    }
}
