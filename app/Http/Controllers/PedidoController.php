<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Item;

class PedidoController extends Controller
{
    public function show(Request $request, $idPedido) {

        $pedido = Pedido::with(['itens', 'cliente'])->find($idPedido);

        if (empty($pedido)) {
            return response()->json(["success" => false, "message" => "pedido não encontrado"], 404);
        }

        return response()->json(["success" => true, "pedido" => $pedido], 200);
    }

    public function create(Request $request) {
        $validated = $this->validate( $request , [
            'cliente.nome' => 'required|max:100',
            'cliente.email' => 'required|email',
            'cliente.cpf' => 'required|digits:11',
            'cliente.cep' => 'required|digits:8',

            'itens' => 'required|array|min:1',
            'itens.*.sku' => 'required|max:30',
            'itens.*.descricao' => 'required|max:150',
            'itens.*.valor' => 'required|numeric|between:0.05,999999.99',
            'itens.*.quantidade' => 'required|numeric|between:1,9999999',

            'frete' => 'required|numeric|between:0,999999.99',
        ]);

        DB::beginTransaction();

        try{

            $cliente = Cliente::firstOrNew(
                ['cpf' => $request->cliente['cpf']],
                [
                    'nome' => $request->cliente['nome'],
                    'email' => $request->cliente['email'],
                    'cep' => $request->cliente['cep'],
                ]
            );

            $cliente->save();

            $sum = function ($valor, $item) {
                return $valor += ($item['valor'] * $item['quantidade']);
            };
            
            $pedido = new Pedido();
            $pedido->cliente_id = $cliente->id;
            $pedido->frete = $request->frete;
            $pedido->valor = $request->frete + array_reduce($request->itens, $sum, 0);
            $pedido->save();

            foreach ($request->itens as $item) {
                $itemNovo = new Item($item);
                $itemNovo->pedido_id = $pedido->id;
                $itemNovo->save();
            }

            DB::commit();

            return response()->json(["success" => true, "pedido" => $pedido->id], 200);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
