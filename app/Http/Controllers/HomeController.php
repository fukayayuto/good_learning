<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entry;
use App\Models\ReservationSetting;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
use App\Models\Information;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {

        // $title = 'これはテストです';
        // $link_part = 'テスト';
        // $part_first = strstr($title,$link_part,true);
        // $left_part = strstr($title,$link_part);
        // $word_count = mb_strlen($link_part);
        // $part_final = mb_substr($left_part, $word_count);
        // die();

        //ログイン情報
        if (empty(Auth::user())) {
            return redirect('/login');
        }
        $user = Auth::user();

        //予約情報表示
        $entry = new Entry();
        $reservation = new ReservationSetting();
        $entry_data = $entry->userSelectEntry($user->id);

        $data = [];

        foreach ($entry_data as $k => $val) {
            $tmp = [];
            $reservation_data = $reservation->userSelectReservation($val['reservation_id']);
            $tmp['start_date'] = $reservation_data->start_date;
            $tmp['progress'] = $reservation_data->progress;
            $tmp['place'] = $reservation_data->place;
            $tmp['count'] = $val['count'];
            $tmp['entry_id'] = $val['id'];
            $data[$k] = $tmp;
        }


        //インフォメーション優先項目
        $information = new Information();
        $information_datas = $information->getPriorityData();


        $information_priority_data = [];
        $count = 6;

        foreach ($information_datas as $k => $val) {
            $tmp = [];
            $title = $val->title;
            $link_part = $val->link_part;
            $part_first = strstr($title, $link_part, true);
            $left_part = strstr($title, $link_part);
            $word_count = mb_strlen($link_part);
            $part_final = mb_substr($left_part, $word_count);
            $date = new Carbon($val->created_at);

            $tmp['title'] = $title;
            $tmp['link_part'] = $link_part;
            $tmp['link'] = $val->link;
            $tmp['part_first'] = $part_first;
            $tmp['part_final'] = $part_final;
            $tmp['created_at'] = $date->format('Y.m.d');
            $count = $count - 1;
            $information_priority_data[$k] = $tmp;
        }


        $information = new Information();
        $information_datas = $information->getLatestData($count);

        $information_data = [];

        foreach ($information_datas as $k => $val) {
            $tmp = [];
            $title = $val->title;
            $link_part = $val->link_part;
            $part_first = strstr($title, $link_part, true);
            $left_part = strstr($title, $link_part);
            $word_count = mb_strlen($link_part);
            $part_final = mb_substr($left_part, $word_count);
            $date = new Carbon($val->created_at);

            $tmp['title'] = $title;
            $tmp['link_part'] = $link_part;
            $tmp['link'] = $val->link;
            $tmp['part_first'] = $part_first;
            $tmp['part_final'] = $part_final;
            $tmp['created_at'] = $date->format('Y.m.d');
            $information_data[$k] = $tmp;
        }

        return view('/dashboard', compact('data', 'user', 'information_data', 'information_priority_data'));
    }

    public function reservation_detail($id)
    {
        $entry = new Entry();
        $data = $entry->selectEntry($id);

        $reservation = new ReservationSetting();
        $reservation_data = $reservation->userSelectReservation($data->reservation_id);
        $start_date = $reservation_data->start_date;
        $progress = (int) $reservation_data->progress;
        $s_date = new Carbon($start_date);
        $end_date= $s_date->addDays($progress)->format('Y-m-d');
        return view('/reservation/detail', compact('data'))->with('start_date', $start_date)->with('end_date', $end_date);
    }
}
