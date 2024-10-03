<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use App\Services\Interfaces\UserInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService implements UserInterface
{

    public function __construct(private GeneralService $generalService)
    {
        $this->generalService = $generalService;
    }


    public function index($request)
    {
        $data = User::with([
            'roles',
            'photo',
            'teams',
            'organizational_role'
        ])->where('company_id', Auth::user()->company_id);

        if (isset($request['roles']) && count($request['roles'])) {
            $roles = $request['roles'];
            $data = $data->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('name', $roles);
            });
        }

        if ($request['perpage'] == 'All') {
            return $this->generalService->handleAllData($request, $data);
        } else {
            return $this->generalService->handlePagination($request, $data);
        }
    }

    public function store($request)
    {
        $user_password = $this->generateStrongPassword(10);
        $request['company_id'] = Auth::user()->company_id;
        $request['name'] = $request['first_name'] . ' ' . $request['last_name'];
        $request['password'] = Hash::make($user_password);
        $staff = User::create($request);
        $staff->assignRole($request['role']);
        return $staff;
    }

    public function show($id)
    {
        $user = User::where('id', $id)->with('photo', 'roles')->first();
        $documents = Document::where('user_id', $id)->get()->groupBy('type');
        return ['data' => $user, 'documents' => $documents];
    }

    public function update($staff, $request)
    {
        $staff->update($request);
        return $staff;
    }


    public function generateStrongPassword($length = 8)
    {
        $characters = '0123456789abcdABCDVWXYZ!@#$%^&*()-_';
        $password = '';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $password;
    }
}
