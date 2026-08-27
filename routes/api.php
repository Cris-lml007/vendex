<?php

use App\Enums\Role;
use App\Enums\Status;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test',function(){
    return response()->json(['message' => 'hola es un test']);
});


//tenants;
Route::prefix('/tenants')->group(function(){
    Route::get('/',function(){
        return response()->json(Tenant::all());
    });

    Route::post('/',function(Request $request){
        $request->validate([
            'id' => 'required|unique:tenants,id',
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'expired' => 'required',
            'status' => 'required',
            'domain' => 'required|unique:domains,domain',
            'user_id' => 'required'
        ]);

        try{
            return DB::transaction(function() use ($request){
                $tenant = Tenant::create([
                    'id' => $request->id,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'plan' => $request->plan,
                    'status' => $request->status,
                    'expired' => $request->expired,
                    'user_id' => $request->user_id
                ]);

                $tenant->domains()->create([
                    'domain' => $request->domain
                ]);

                $tenant->domains;

                return response()->json([
                    'message' => 'Tenant Creado Satisfactoriamente',
                    'tenant' => $tenant
                ],200);

            });
        }catch(Exception $e){
            return response()->json(['message' => 'Error', 'error' => $e->getMessage()],500);
        }
    });

    Route::get('/{tenant}',function(Tenant $tenant){
        $tenant->domains;
        return response()->json([$tenant]);
    });


    Route::post('/{tenant}/active',function(Tenant $tenant){
        $tenant->status = Status::ACTIVE;
        $tenant->save();
        return response()->json(['message' => "{$tenant->name}: Activo"],200);
    });

    Route::post('/{tenant}/suspend',function(Tenant $tenant){
        $tenant->status = Status::BLOCKED;
        $tenant->save();
        return response()->json(['message' => "{$tenant->name}: Suspendido"],200);
    });

    Route::post('/{tenant}/disabled',function(Tenant $tenant){
        $tenant->status = Status::DISABLE;
        $tenant->save();
        return response()->json(['message' => "{$tenant->name}: Deshabilitado"],200);
    });

    Route::put('/{tenant}/expired',function(Request $request, Tenant $tenant){
        $tenant->expired = $request->expired;
        $tenant->save();
        return response()->json(['message' => "expiracion cambiado a {$request->expired}"],200);
    });

    //domains;
    Route::get('/{tenant}/domains',function(Tenant $tenant){
        return response()->json($tenant->domains);
    });

    Route::post('/{tenant}/domains',function(Request $request,Tenant $tenant){
        $request->validate([
            'domain' => 'required|unique:domains,domain',
        ]);

        $tenant->domains()->create([
            'domain' => $request->domain
        ]);

        return response()->json(['message' => 'dominio registrado'],201);
    });

    Route::put('/{tenant}/domains',function(Request $request,Tenant $tenant){
        $request->validate([
            'domain' => ['required', Rule::exists('domains','domain')
                ->where('tenant_id',$tenant->id)
            ],
            'new_domain' => 'required|unique:domains,domain'
        ]);

        $tenant->domains()
            ->where('domain',$request->domain)
            ->update([
                'domain' => $request->new_domain
            ]);

        return response()->json(['message' => 'dominio actualizado'],200);
    });

    Route::delete('/{tenant}/domains',function(Request $request,Tenant $tenant){
        $request->validate([
            'domain' => ['required', Rule::exists('domains','domain')
                ->where('tenant_id',$tenant->id)
            ]
        ]);

        $tenant->domains()
            ->where('domain',$request->domain)
            ->delete();

        return response()->json(['message' => 'dominio Eliminado'],200);
    });


    //users;
    Route::get('/{tenant}/users',function(Tenant $tenant){
        return $tenant->run(function(){
            return response()->json(User::all());
        });
    });

    Route::post('/{tenant}/users/create', function(Tenant $tenant){
        return $tenant->run(function(){
            $p = Str::random(8);
            if(User::where('username','admin')->exists()){
                return response()->json(['message' => 'usuario admin existente'],200);
            }else{
                $u = User::create([
                    'name' => 'admin',
                    'username' => 'admin',
                    'password' => $p,
                    'status' => Status::ACTIVE,
                    'role' => Role::ADMIN
                ]);

                if($u){
                    return response()->json([
                        'message' => 'Usuario creado Existosamente',
                        'username' => 'admin',
                        'password' => $p,
                        'status' => 'Activo'
                    ],201);
                }
            }
        });
    });

    Route::put('/{tenant}/users/reset-password',function(Request $request, Tenant $tenant){
        return $tenant->run(function() use ($request){
            $request->validate([
                'username' => 'required|exists:users,username'
            ]);
            $password = Str::random(8);
            User::where('username', $request->username)
                ->update([
                    'password' => $password
                ]);
            return response()->json([
                'message' => 'contraseña Restablecida: '. $request->username,
                'password' => $password
            ], 200);
        });
    });
});





