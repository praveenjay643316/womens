<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;

/**
 * Full CRUD for the Users resource: list, create, store, edit, update, destroy.
 */
class UserController extends Controller
{
    public function index(Request $request): string
    {
        $users = User::all('created_at DESC');

        return $this->view('users.index', ['users' => $users]);
    }

    public function create(Request $request): string
    {
        return $this->view('users.create');
    }

    public function store(Request $request): never
    {
        $data = $request->validate([
            'name'  => 'required|max:100',
            'email' => 'required|email|unique:users,email',
        ]);

        User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->redirect('/users');
    }

    public function edit(Request $request, string $id): string
    {
        $user = User::find($id);

        if (!$user) {
            Response::abort(404, 'User not found.');
        }

        return $this->view('users.edit', ['user' => $user]);
    }

    public function update(Request $request, string $id): never
    {
        $user = User::find($id);

        if (!$user) {
            Response::abort(404, 'User not found.');
        }

        $data = $request->validate([
            'name'  => 'required|max:100',
            'email' => "required|email|unique:users,email,{$id}",
        ]);

        User::update($id, [
            'name'       => $data['name'],
            'email'      => $data['email'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->redirect('/users');
    }

    public function destroy(Request $request, string $id): never
    {
        User::delete($id);

        $this->redirect('/users');
    }
}
