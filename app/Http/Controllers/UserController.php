<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request){
        $subUsers=User::where('parent_id',Auth::user()->id)->get();
        $allPermissions=Permission::all();
        return view('user.subusers.list', compact('subUsers','allPermissions'));
    }
    public function create(Request $request){
        try {
            $validator = Validator::make($request->all(),[
                'name'=>'required|max:127',
                'email'=>'required|email',
                'user_permission'=>'required',
                'password'=>['required','confirmed',Password::min(8)]
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'validaion_error' => $validator->errors()->all()
                ],200);
            }
            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'password' => Hash::make($request->password),
                'parent_id' => $request->parent_id
            ]);
            foreach ($request->user_permission as $permission){
                DB::table('users_permissions')
                    ->insert([
                    'user_id'=>$newUser->id,
                    'permission_id'=>$permission
                ]);
            }


            return response()->json([
                'success' => true,
                'message' => "User created successfully!",
                'user'=>$newUser
            ],200);

        } catch (Exception $e) {
            //throw $th;
        }
    }
    public function destroy(User $user) {
        try {
            $user->delete();
            return response()->json([
                'success' => true,
                'message' =>'User deleted successfully'
            ],200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' =>$e->getMessage()
            ],200);
        }
    }
    public function show($id){
        $user=User::where('id',$id)->first();
        if($user){
            $userPermissions=[];
            $permissions= DB::table('users_permissions')->select("permission_id")->where('user_id',$user->id)->get();
            foreach($permissions as $permission){
                $userPermissions[]=$permission->permission_id;
            }
            return response()->json([
                'success' => true,
                'message' => "User fetched successfully!",
                'user'=>$user,
                'permissions'=>$userPermissions
            ],200);
        }else{
            return response()->json([
                'success' => false,
                'message' => "No user found"
            ],200);
        }
    }
    public function update(Request $request){
        try {
            $validator = Validator::make($request->all(),[
                'name'=>'required|max:127',
                'email'=>'required|email',
                'user_permission'=>'required',
                'password'=>['sometimes','nullable','min:8','confirmed',Password::min(8)]
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'validaion_error' => $validator->errors()->all()
                ],200);
            }

            $user=User::where('id',$request->user_id)->first();
            if($user){
                $updateData=array();
                $updateData['name']=$request->name;
                $updateData['email']=$request->email;
                if($request->password){
                    $updateData['password']=Hash::make($request->password);
                }
                $user->update($updateData);

                //update permissions
                DB::table('users_permissions')->where('user_id', '=', $user->id)->delete();
                foreach ($request->user_permission as $permission){
                    DB::table('users_permissions')
                        ->insert([
                        'user_id'=>$user->id,
                        'permission_id'=>$permission
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "User updated successfully!"
            ],200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error while updating - " . $e->getMessage()
            ],200);
        }
    }
}
