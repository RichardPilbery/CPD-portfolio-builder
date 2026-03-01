<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Activity;
use App\Models\Audit;
use App\Models\Audititem;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $portfolios = auth()->user()->portfolios->sortByDesc('actdate')->take(10);
        $audits = auth()->user()->audits->sortByDesc('incdatetime')->take(10);
        $user = auth()->user();

       
        $portfolio_bar_data = DB::select(DB::raw(
            "select count(*) as total, a.abbr, a.last2years
            from
            (select 
            activity_id, 
            user_id
            name, 
            abbr,
            actdate,
            date_add(now(),interval -2 year) as twoyears,
            (case when actdate > date_add(now(),interval -2 year) then 'Last 2 years' else '>2 years' end) as last2years
            from portfolios 
            left join activities on portfolios.activity_id=activities.id where user_id = '".$user->id."') a
            group by a.abbr, a.last2years"
        ));
        //auth()->user()->portfolios->with('Activity')->take(10);
        //dd($portfolio_bar_data);
        // $bar_data = json_encode($portfolio_bar_data);
        // dd($portfolio_bar_data);

        $audit_line_data = DB::select(DB::raw(
            "select
                count(*) as y,
                DATE_FORMAT(incdatetime, '%Y-%m') AS x,
                user_id
            FROM audits where user_id = '".$user->id."'
            group by DATE_FORMAT(incdatetime, '%Y-%m'), user_id
            order by DATE_FORMAT(incdatetime, '%Y-%m')"
        ));

        //dd($audit_line_data);


        $prep_bar = [];
        $prev = '';
        foreach ($portfolio_bar_data as $row) {
            if($row->last2years != $prev) {
                $prev = $row->last2years;
            }
            $prep_bar[$prev][] = $row;
        }
        // dd($prep_bar);

        $ampds = AuditItem::where('name', 'AMPDS code')->pluck('name2', 'id')->toArray();
        asort($ampds);

        $act_abbr = Activity::get()->pluck('abbr','id');
        $act_name = Activity::get()->pluck('name','id');

        return view('home', compact(['portfolios','act_abbr','act_name', 'audits','user','ampds', 'prep_bar', 'audit_line_data']));

    }

    public function cookie() {
        return view('cookie');
    }

    public function about() {
        return view('about');
    }

    public function ksf() {
        return view('ksf');
    }

    public function cpd() {
        return view('cpd');
    }

    public function hcpc() {
        $now = date("Y");
        $month = date("m");
        //$month = 10;
        //$now = 2009;
        $odd = $now%2;
        if($odd) {
            //it's an odd year, so we are auditing
            //echo "The month is $month<br/>";
            if($month < 9) {
                //It is on or before August
                //echo "It's odd and less than August<br/>";
                $this_audit = $now;
                $last_audit = $now - 2;
            }
            else {
                //echo "It's odd and after August<br/>";
                $this_audit = $now+2;
                $last_audit = $this_audit - 2;
            }
        }
        else {
            //echo "It's even month<br/>";
            $this_audit = $now+1;
            $last_audit = $this_audit-2;
        }

        return view('hcpc', compact(['this_audit', 'last_audit']));
    }

    public function pdps() {
        $now = date("Y");
        $month = date("m");
        //$month = 10;
        //$now = 2009;
        $odd = $now%2;
        if($odd) {
            //it's an odd year, so we are auditing
            //echo "The month is $month<br/>";
            if($month < 9) {
                //It is on or before August
                //echo "It's odd and less than August<br/>";
                $this_audit = $now;
                $last_audit = $now - 2;
            }
            else {
                //echo "It's odd and after August<br/>";
                $this_audit = $now+2;
                $last_audit = $this_audit - 2;
            }
        }
        else {
            //echo "It's even month<br/>";
            $this_audit = $now+1;
            $last_audit = $this_audit-2;
        }
        $date1 = time();
        $date2 = mktime(0,0,0,8,31,$this_audit);
        $dateDiff = $date2-$date1;
        //echo "Now is $date1 and aug 31st is $date2 Date diff is $dateDiff";
        $fullDays = floor($dateDiff/86400);

        return view('pdp', compact(['fullDays']));
    }
}
