<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BantuanController extends Controller
{
    /**
     * Tampilkan halaman Bantuan / Customer Service Chatbot.
     */
    public function index()
    {
        return view('bantuan.index');
    }
}