<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        // return view('auth.admin-login');
        return view('admin/login');
    }

    //Redirección al login (Prueba)
    // protected function unauthenticated($request, array $guards)
    // {
    //     return redirect()->guest(route('admin.login'));
    // }

    public function login(Request $request)
    {
        $credentials = $request->only('CORREO', 'CONTRASENIA');

        // Intentar autenticar al administrador
        if (Auth::guard('admin')->attempt([
            'CORREO' => $credentials['CORREO'],
            'password' => $credentials['CONTRASENIA'], // Laravel verificará la contraseña encriptada
        ])) {
            // Autenticación exitosa
            return redirect()->intended('admin/registrar-cuenta'); // Cambiar esta ruta según sea necesario
        }

        // Autenticación fallida
        return redirect()->back()->withErrors([
            // 'CORREO' => 'Las credenciales no coinciden con nuestros registros.',

        ]);
    }

    public function logout()
    {
        Auth::guard('admin')->logout(); // Cierra la sesión del admin
        return redirect('/admin/login'); // Redirige al login
    }

    public function mostrarPaginaRegistrar()
    {
        return view('admin.registrar-cuenta');  
    }

    public function mostrarListado()
    {
        $admins = Admin::all(); // Obtener todos los administradores
        return view('admin.listado-cuentas', compact('admins'));  
    }

    public function registrarCuenta(Request $request)
    {
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'NOMBRE' => '',
            'CORREO' => 'required|email|max:30|unique:admin,CORREO',
            'CONTRASENIA' => '',
            'TIPO' => 'required|integer',
        ]);
    
        // Crear el nuevo admin
        Admin::create([
            'NOMBRE' => $validatedData['NOMBRE'],
            'CORREO' => $validatedData['CORREO'],
            'CONTRASENIA' => bcrypt($validatedData['CONTRASENIA']), // Encriptar la contraseña
            'TIPO' => $validatedData['TIPO'],
        ]);
    
        // Redirigir a una página de confirmación o al listado de cuentas
        return redirect()->route('admin.listado-cuentas')->with('success', 'Cuenta creada exitosamente');
    }    

}
