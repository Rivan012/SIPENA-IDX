<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    public function index($kegiatan) {}
    public function updateStatus(Request $request, $peserta) {}
    public function destroy($peserta) {}
}
