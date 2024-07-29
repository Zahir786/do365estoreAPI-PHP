<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $maintenanceStatus = config('constants.MAINTENANCE_STATUS');
        if($maintenanceStatus == 1) {
            $responseArr = array(
                "error" => 1,
                "errorCode" => 106,
                "msg" => "We are under maintenance. Please try again later!",
                "status" => "failure",
                "maintenanceData" => [
                    'maintenanceStatus' => config('constants.MAINTENANCE_STATUS'),
                    'maintenanceStartDate' => config('constants.MAINTENANCE_START_DATE'),
                    'maintenanceEndDate' => config('constants.MAINTENANCE_END_DATE'),
                ]
            );

            return response()->json($responseArr);
        }
        
        return $next($request);
    }
}
