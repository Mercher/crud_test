<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(int $per_page = 15) {
        $users = User::paginate($per_page);
        return response()->json([$users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request) {



        $password = bcrypt($request->input('password'));
        try {
            $user = new User();
            $request->merge(['password' => $password]);
            $user->fill($request->input());
            $user->save();
            return response()->json([$user], 201);
        } catch (\Throwable $th) {
            return response()->json(['Errore creazione user' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id) {
        $user = User::findOrFail($id);
        return response()->json([$user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id) {

        $request->validate([
            'name' => 'required|max:255',
            'surname' => 'required|max:255',
            'birth_date' => 'required|date',
            'email' => 'required|unique:users',
            'password' => 'required',
            'fiscal_code' => 'nullable|regex:/^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/i'
        ]);

        $password = bcrypt($request->input('password'));
        try {
            $user = User::findOrFail($id);
            $user->fill($request->input());
            $user->save();

            if ($request->input('password') != '') {
                $this->changePassword($password, $id);
            }

            return response()->json($user);
        } catch (\Throwable $th) {
            return response()->json(['Errore aggiornamento utente' => $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id) {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['status' => 'Eliminato']);
        } catch (\Throwable $th) {
            return response()->json(['Errore eliminazione utente' => $th->getMessage()]);
        }
    }

    private function changePassword(string $password, int $id) {
        $user = User::find($id);
        $user->fill(['password' => $password]);
        $user->save();
    }
}
