<?php

namespace App\Http\Controllers\info_arbitral;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\MPartido;
use App\Http\Models\MEquipo;
use App\Http\Models\MTorneo;
use App\Http\Models\MJugador;
use App\Http\Models\MFase;
use App\Http\Models\MJugadorEquipo;
use App\Http\Models\MGol;
use App\Http\Models\MTarjeta;
use App\Http\Models\MTitular;
use App\Http\Models\MSuplente;
use App\Http\Models\MCambio;
use Codedge\Fpdf\Facades\Fpdf;
use DB;



class partidos_controller extends Controller
{
    public function partidos_hoy($fecha,$fase,$torneo){
       $Partidos = MPartido::select('*')
       ->where('fecha',$fecha)->where('tipo_fase',$fase)->where('id_torneo',$torneo)
       ->get(); 

       foreach($Partidos as  $valor) {
            $Equipos1 = MEquipo::where('id_equipo','=',$valor->equipo_local)->first();
            $valor->equipo_local=$Equipos1->nombre;

            $Equipos2 = MEquipo::where('id_equipo','=',$valor->equipo_visitante)->first();
            $valor->equipo_visitante=$Equipos2->nombre;
           // print "\n"."equipos".$valor;
       }
       return $Partidos;
    }
    public function fases_x_categoria($id) {
       //print "\n".$id;
        $enviar = MTorneo::where('id_torneo',$id)->take(1)->first();
        return view('info_arbitral/fases')->with('torneo',$enviar);
    
    }

    public function getCategorias() {
        $Torneos = MTorneo::select('*')->where('elimnado',false)->get();
        return $Torneos;
    }


    public function fases_categoria($Torneo) {
        $enviar = MPartido::select('tipo_fase')->where('id_torneo',$Torneo)->groupBy('tipo_fase')->get();
        return $enviar;
    }

    public function calendario_x_fase($fase,$torneo) {
        $enviar = MPartido::where('tipo_fase',$fase)->where('id_torneo',$torneo)->take(1)->first();
        //print "\n".$enviar;
        return view('info_arbitral/calendario')->with('torneo',$enviar);
    
    }

    /*Este metodo devuelve la vista del formulario para registrar
    los goles, los jugadores titulares y los jugadores suplentes*/
    public function verRegistro($idpar)
    {
        $Partido = MPartido::where('id_partido',$idpar)
        ->take(1)->first();
        return view("info_arbitral\goles")->with('partidoj',$Partido);
    }

    public function verRegistro2($equi)
    {
        $users = MJugadorEquipo::select('*')
        ->where('id_equipo',$equi)
        ->join('jugador', 'jugador_equipo.id_jugador', '=', 'jugador.id_jugador')
        /*->select('*')*/
        ->get();
        return $users;
        /*print "\n"."   fecha  ".$Partido->fecha;
        print "\n"."   lugar  ".$Partido->lugar;
        print "\n"."   equipo1  ".$Partido->equipo_local;
        print "\n"."   equipo2  ".$Partido->equipo_visitante;*/
        //return view('info_arbitral/goles')->with('elocal',$Partido->equipo_local);
    }



    public function insertagol(Request $request/*$id_partidol,$goljugadorl,$golminutol,$contradel,$favordel,$tipol*/)
    {
        //print($request->id_jugador[0]);

        for($i=0; $i<count($request->minutol); $i++)
            {
                $id_partido = $request->id_partido[0];
                $id_equipo = $request->equipol[0];
                $id_jugador = $request->id_jugadorl[$i];
                $minuto = $request->minutol[$i];
            
                //print(""+$id_partido);

                MGol::insert(['id_partido'=>$id_partido,'id_equipo'=>$id_equipo,'id_jugador'=>$id_jugador,
                            'minuto'=>$minuto]);
            }


        for($i=0; $i<count($request->minutov); $i++)
            {
                $id_partido = $request->id_partido[0];
                $id_equipo = $request->equipov[0];
                $id_jugador = $request->id_jugadorv[$i];
                $minuto = $request->minutov[$i];
            
                //print(""+$id_partido);

                MGol::insert(['id_partido'=>$id_partido,'id_equipo'=>$id_equipo,'id_jugador'=>$id_jugador,
                            'minuto'=>$minuto]);
            }
        //return back();
    }

    public function insertatitulares(Request $request/*$id_partidol,$goljugadorl,$golminutol,$contradel,$favordel,$tipol*/)
    {
        //print($request->id_jugador[0]);
        
        for($i=0; $i<count($request->id_jugadorl); $i++)
        {
            $id_partido = $request->id_partido[0];
            $id_equipo = $request->equipol[0];
            $id_jugador = $request->id_jugadorl[$i];
            
            //print(""+$id_partido);

            MTitular::insert(['id_partido'=>$id_partido,'id_equipo'=>$id_equipo,'id_jugador'=>$id_jugador]);
        }

        for($i=0; $i<count($request->id_jugadorv); $i++)
        {
            $id_partido = $request->id_partido[0];
            $id_equipo = $request->equipov[0];
            $id_jugador = $request->id_jugadorv[$i];
            
            //print(""+$id_partido);

            MTitular::insert(['id_partido'=>$id_partido,'id_equipo'=>$id_equipo,'id_jugador'=>$id_jugador]);
        }
        //return back();
    }

    public function insertatarjeta(Request $request/*$id_partidol,$goljugadorl,$golminutol,$contradel,$favordel,$tipol*/)
    {
        //print($request->id_jugador[0]);
        
        for($i=0; $i<count($request->minutol); $i++)
        {
            $id_partido = $request->id_partido[0];
            $id_jugador = $request->id_jugadorl[$i];
            $minuto = $request->minutol[$i];
            $tipo = $request->tfaltal[$i];
            
            //print(""+$id_partido);

            MTarjeta::insert(['id_partido'=>$id_partido,'id_jugador'=>$id_jugador,'minuto'=>$minuto,
                    'tipo'=>$tipo]);
        }

        for($i=0; $i<count($request->minutov); $i++)
        {
            $id_partido = $request->id_partido[0];
            $id_jugador = $request->id_jugadorv[$i];
            $minuto = $request->minutov[$i];
            $tipo = $request->tfaltav[$i];
            
            //print(""+$id_partido);

            MTarjeta::insert(['id_partido'=>$id_partido,'id_jugador'=>$id_jugador,'minuto'=>$minuto,
                    'tipo'=>$tipo]);
        }
        //return back();
    }


    public function insertasuplentes(Request $request/*$id_partidol,$goljugadorl,$golminutol,$contradel,$favordel,$tipol*/)
    {
        //print($request->id_jugador[0]);
        
        for($i=0; $i<count($request->id_jugadorl); $i++)
        {
            $id_partido = $request->id_partido[0];
            $id_equipo = $request->equipol[0];
            $id_jugador = $request->id_jugadorl[$i];
            
            //print(""+$id_partido);

            MSuplente::insert(['id_partido'=>$id_partido,'id_equipo'=>$id_equipo,'id_jugador'=>$id_jugador]);
        }

        for($i=0; $i<count($request->id_jugadorv); $i++)
        {
            $id_partido = $request->id_partido[0];
            $id_equipo = $request->equipov[0];
            $id_jugador = $request->id_jugadorv[$i];
            
            //print(""+$id_partido);

            MSuplente::insert(['id_partido'=>$id_partido,'id_equipo'=>$id_equipo,'id_jugador'=>$id_jugador]);
        }
        //return back();
    }

    public function insertacambios(Request $request/*$id_partidol,$goljugadorl,$golminutol,$contradel,$favordel,$tipol*/)
    {
        //print($request->id_jugador[0]);
        
        for($i=0; $i<count($request->id_jugadorel); $i++)
        {
            $id_partido = $request->id_partido[0];
            $id_equipo = $request->equipol[0];
            $jugador_entra = $request->id_jugadorel[$i];
            $jugador_sale = $request->id_jugadorsl[$i];
            $minuto = $request->minutol[$i];
            
            //print(""+$id_partido);

            MCambio::insert(['id_partido'=>$id_partido,'id_equipo'=>$id_equipo,'jugador_entra'=>$jugador_entra,
                            'jugador_sale'=>$jugador_sale,'minuto'=>$minuto]);
        }

        for($i=0; $i<count($request->id_jugadorev); $i++)
        {
            $id_partido = $request->id_partido[0];
            $id_equipo = $request->equipov[0];
            $jugador_entra = $request->id_jugadorev[$i];
            $jugador_sale = $request->id_jugadorsv[$i];
            $minuto = $request->minutov[$i];
            
            //print(""+$id_partido);

            MCambio::insert(['id_partido'=>$id_partido,'id_equipo'=>$id_equipo,'jugador_entra'=>$jugador_entra,
                            'jugador_sale'=>$jugador_sale,'minuto'=>$minuto]);
        }
        //return back();
    }

    public function pdfResultados(){
        $date1 = date("Y-m-d"); 
        $al = MTorneo::select('id_torneo','nombre','categoria','fecha_inaguracion','fecha_termino','elimnado')
		->where('fecha_termino','>=',$date1)->get();
        $pathFile = storage_path(). '/recipe.pdf';
        Fpdf::AddPage();
        Fpdf::SetFont('Courier', 'B', 30);
        $i=0;

        Fpdf::Cell(190,9,'TORNEOS EN CURSO',1,1,'C',false);
        Fpdf::Cell(60,10,'',0,1,'C');

        Fpdf::SetFont('Courier', 'B', 14);
        Fpdf::Cell(10,3*$i,'ID');
        Fpdf::Cell(50,3*$i,utf8_decode('NOMBRE'));
        Fpdf::Cell(30,3*$i,'CATEGORIA');
        Fpdf::Cell(50,3*$i,'FECHA INICIO');
        Fpdf::Cell(20,3*$i,'FECHA TERMINO',0,1,'C');

        Fpdf::Cell(20,3*$i,'',0,1,'C');
        $i++;
        Fpdf::Cell(20,3*$i,'',0,1,'C');
        $i++;

        Fpdf::SetFont('Arial', '', 12);
        foreach($al as $key => $value){
            if($value->elimnado != true){
            Fpdf::Cell(10,5*$i,$value->id_torneo);
            Fpdf::Cell(60,5*$i,utf8_decode($value->nombre));
            Fpdf::Cell(20,5*$i,utf8_decode($value->categoria));
            Fpdf::Cell(50,5*$i,$value->fecha_inaguracion);
            Fpdf::Cell(20,5*$i,$value->fecha_termino,0,1,'C');
            }
        }

        Fpdf::Output('F', $pathFile);
        $headers = ['Content-Type' => 'application/pdf'];
        return response()->file($pathFile, $headers);
    }


    /*public function jugadores_equipo($equipo){
        $users = MJugadorEquipo::select('*')
            ->where('id_equipo',$equipo)
            ->join('jugador', 'jugador_equipo.id_jugador', '=', 'jugador.id_jugador')
            ->select('*')
            ->get();
            print $users;
       }*/

     /*public function dbtest(){
        $users = DB::select('select * from equipo ');
        return view('info_arbitral/tablageneral')->with('users',$users);
        //return view('user.index', ['users' => $users]);
     } */ 
}
?>