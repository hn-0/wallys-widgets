<?php

namespace App\Http\Controllers;

use App\Services\WidgetService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WidgetController extends Controller
{
    /**
     * The widget calculator form.
     *
     * @param  Request  $request
     *
     * @return View|RedirectResponse
     */
    public function widgetCalculatorForm(Request $request): View|RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            ['order-quantity' => 'numeric|integer|gt:0'],
            ['order-quantity' => 'Please enter a non-zero positive integer.'],
        );

        if ($validator->fails()) {
            return redirect()->route('home')->withErrors($validator->errors());
        }

        if ($request->has('order-quantity')) {
            $orderQuantity = $request->get('order-quantity');

            $viewParams = [
                'orderQuantity' => $orderQuantity,
                'minPacksRequired' => WidgetService::getMinimumPacksRequired($orderQuantity),
            ];
        } else {
            $viewParams = [];
        }

        return view('widget-calculator', $viewParams);
    }
}
