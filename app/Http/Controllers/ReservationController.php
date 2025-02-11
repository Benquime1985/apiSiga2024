<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReservationCollection;
use App\Http\Responses\ApiResponse;
use App\Models\Reservation;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $reservations = new ReservationCollection(Reservation::all());
            return ApiResponse::success('Listado De las Reservaciones',201,$reservations);
        } catch (Exception $e){
            return ApiResponse::error($e->getMessage(),500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try { //? Validación de los datos recibidos
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'space_id' => 'required|exists:spaces,id',
                'reservation_date' => 'required|date',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'status' => 'required|string|max:20',
                'uploaded_job' => ['nullable', 'file', 'mimes:pdf,doc,docx,txt', 'max:20480'], //? Archivo opcional (hasta 20MB)
                'reservation_details' => 'nullable|string',
            ]);
            $reservation = new Reservation(); //? Crear una nueva reservación
            $reservation->user_id = $request->input('user_id');
            $reservation->space_id = $request->input('space_id');
            $reservation->reservation_date = $request->input('reservation_date');
            $reservation->start_date = $request->input('start_date');
            $reservation->end_date = $request->input('end_date');
            $reservation->start_time = $request->input('start_time');
            $reservation->end_time = $request->input('end_time');
            $reservation->status = $request->input('status');
            $reservation->reservation_details = $request->input('reservation_details');
            if ($request->hasFile('uploaded_job')) { //? Manejo de archivo si es que se sube
                $file = $request->file('uploaded_job');
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $name_file = str_replace(" ", "_", $filename); //? Reemplaza espacios por _
                $extension = $file->getClientOriginalExtension();
                $fileNameToStore = date('His') . '_' . $name_file . '.' . $extension;
                $file->move(public_path('/uploads/Doc_reservations'), $fileNameToStore); //? Mover el archivo a la carpeta 'uploads/reservations'
                $reservation->uploaded_job = '/uploads/Doc_reservations' . $fileNameToStore;
            }
            $reservation->save(); //? Guardar la reservación en la base de datos
            return ApiResponse::success("Reservación creada correctamente.", 200, $reservation);//? Respuesta de éxito
        } catch (ValidationException $e) {
            return ApiResponse::error($e->getMessage(), 422); //? Manejo de errores de validación
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), 500); //? Manejo de cualquier otro error
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try{
            $reservation = new ReservationCollection(Reservation::query()->where('id',$id)->get());
            if($reservation->isEmpty()) throw new ModelNotFoundException("Reservacion No Encontrado");
            return ApiResponse::success( 'Reservacion Encontrada',200,$reservation);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Reservacion No Encontrada',404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try { 
            $reservation = Reservation::findOrFail($id); //? Encontrar la reservación o lanzar una excepción si no existe
            //? Validación de los datos recibidos
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'space_id' => 'required|exists:spaces,id',
                'reservation_date' => 'required|date',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'status' => 'required|string|max:20',
                'uploaded_job' => ['nullable', 'file', 'mimes:pdf,doc,docx,txt', 'max:20480'], //? Archivo opcional (hasta 20MB)
                'reservation_details' => 'nullable|string',
            ]);
            $reservation->user_id = $request->input('user_id');
            $reservation->space_id = $request->input('space_id');
            $reservation->reservation_date = $request->input('reservation_date');
            $reservation->start_date = $request->input('start_date');
            $reservation->end_date = $request->input('end_date');
            $reservation->start_time = $request->input('start_time');
            $reservation->end_time = $request->input('end_time');
            $reservation->status = $request->input('status');
            $reservation->reservation_details = $request->input('reservation_details');
            if ($request->hasFile('uploaded_job')) { //? Manejo de archivo si es que se sube
                $file = $request->file('uploaded_job');
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $name_file = str_replace(" ", "_", $filename); //? Reemplaza espacios por _
                $extension = $file->getClientOriginalExtension();
                $fileNameToStore = date('His') . '_' . $name_file . '.' . $extension;
                $file->move(public_path('/uploads/Doc_reservations'), $fileNameToStore); //? Mover el archivo a la carpeta 'uploads/reservations'
                $reservation->uploaded_job = '/uploads/Doc_reservations' . $fileNameToStore;
            }
            $reservation->save(); //? Guardar la reservación en la base de datos
            return ApiResponse::success("Reservación Actualizada correctamente.", 200, $reservation);//? Respuesta de éxito
        } catch (ValidationException $e) {
            return ApiResponse::error($e->getMessage(), 422); //? Manejo de errores de validación
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), 500); //? Manejo de cualquier otro error
        } catch(ModelNotFoundException $e){
            return ApiResponse::error('No se encontro la Reservacion', 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $reservation= Reservation::findOrFail($id);
            $reservation->delete();
            return ApiResponse::success("Se ha eliminado la reservacion de manera exitosa!!", 200);
        }catch (ModelNotFoundException $e) {
            return ApiResponse::error("La reservacion no existe",404);
        }catch (Exception $e){
            return ApiResponse::error($e->getMessage(),500);
        }
    }
}
