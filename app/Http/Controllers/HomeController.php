<?php

namespace App\Http\Controllers;
use App\Models\Barang;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function barang()
    {
        $buku=Barang::all();
        return view('barang', compact('buku'));
    }
    public function cart(Request $request)
    {
        $token = "ce478e93e58372f0478b30a872717bf8";
        $response = Http::withHeaders([
            'key' => $token,
        ])->get('https://api.rajaongkir.com/starter/city');

        $datas = $response->json();
        $data = $datas['rajaongkir'] ;

        $id=$request->input('id');
        $data_buku=Barang::find($id);
        return view('cart',compact('data_buku', 'data'));
    }
    

    public function cart_ongkir(Request $request)
    {
        $token = "ce478e93e58372f0478b30a872717bf8";
        $asal = $request->input('asal');
        $tujuan = $request->input('tujuan');
        $berat = $request->input('berat');
        $kurir = $request->input('kurir');

        $id=$request->input('id');
        $data_buku=Barang::find($id);

        $response0 = Http::withHeaders([
            'key' => $token,
        ])->get('https://api.rajaongkir.com/starter/city');

        $response = Http::withHeaders([
            'key' => $token,
        ])->post('https://api.rajaongkir.com/starter/cost',
            ['origin' => $asal,
            'destination' => $tujuan,
            'weight' => $berat,
            'courier' => $kurir]);

        if ($response->successful()) {
            $ongkirs = $response->json();
            $ongkir = $ongkirs['rajaongkir']['results'][0] ;

            $datas = $response0->json();
            $data = $datas['rajaongkir'] ;


            return view('cart', compact('ongkir', 'data', 'data_buku', 'ongkirs'));

        } else {
            return $response;
        }


    }


    public function checkout(Request $request){
        $user_id = $request->input('user_id');
        $book_id = $request->input('book_id');
        $total_harga = $request->input('total_harga');
        $tanggal = Carbon::now()->format('Y-m-d');
        $pengiriman = $request->input('pengiriman');

        $Pesanan = new Pesanan();
        $Pesanan->barang_id = $book_id;
        $Pesanan->user_id = $user_id;
        $Pesanan->tanggal = $tanggal;
        $Pesanan->pengiriman = $pengiriman;
        $Pesanan->total_harga = $total_harga;
        $Pesanan->save();

        $pesan = Barang::find($book_id);
        $pesan->stok = $pesan->stok - 1;
        $pesan->save();

        return redirect('home');
    }
    public function admin()
    {
        $data = Barang::all();
        return view('admin', compact('data'));
    }

    public function tambah_barang(Request $request){
        $data = new Barang;
        $data->nama = $request->input('nama');
        $data->harga = $request->input('harga');
        $data->stok = $request->input('stok');

        $namaFoto = $request->file('gambar')->getClientOriginalName();

        $request->file('gambar')->move(public_path('buku'), $namaFoto);

        $data->gambar = $namaFoto;
        $data->save();
        return back();
    }

    public function hapus_barang($id){
        $buku = Barang::find($id);
        $buku->delete();
        return back();
    }
    public function pesanan()
    {
        $data = Pesanan::all();
        return view('pesanan', compact('data'));
    }
}

