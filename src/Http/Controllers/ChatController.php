<?php

namespace MarceliTo\Aicms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ChatController extends Controller
{
    public function index()
    {
        return view('aicms::chat');
    }
}
