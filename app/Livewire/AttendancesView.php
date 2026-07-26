<?php

namespace App\Livewire;

use App\Models\Store;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AttendancesView extends Component
{

    use WithPagination;


    public $users;
    public $stores;


    public $totalUsers = 0;
    public $totalAttendance = 0;
    public $onTime = 0;
    public $late = 0;
    public $withoutExit = 0;
    public $absences = 0;
    public $averageLate = 0;
    public $averageWorked = 0;


    public $from = '';
    public $to = '';
    public $user = '';
    public $store = '';
    public $status = '';


    protected $paginationTheme = 'bootstrap';


    public function mount()
    {
        $this->from = now()->startOfMonth()->format('Y-m-d');
        $this->to = now()->format('Y-m-d');
    }



    public function updated($property)
    {
        if(
            in_array($property,[
                'from',
                'to',
                'user',
                'store',
                'status'
            ])
        ){
            $this->resetPage();
        }
    }



    public function getAttendanceChart()
    {
        return User::query()

            ->leftJoin('attendances',function($join){

                $join->on(
                    'users.id',
                    '=',
                    'attendances.user_id'
                );


                if($this->from){

                    $join->whereDate(
                        'attendances.created_at',
                        '>=',
                        $this->from
                    );

                }


                if($this->to){

                    $join->whereDate(
                        'attendances.created_at',
                        '<=',
                        $this->to
                    );

                }

            })


            ->when($this->store,function($q){

                $q->where(
                    'users.store_id',
                    $this->store
                );

            })


            ->when($this->user,function($q){

                $q->where(
                    'users.id',
                    $this->user
                );

            })


            ->selectRaw("
                users.name,
                COUNT(DISTINCT DATE(attendances.created_at)) attendance
            ")


            ->groupBy(
                'users.id',
                'users.name'
            )


            ->orderByDesc('attendance')

            ->get();

    }

    public function getAttendances()
    {

        return User::query()

            ->leftJoin('stores',
                'users.store_id',
                '=',
                'stores.id'
            )


            ->leftJoin('attendances',
                'users.id',
                '=',
                'attendances.user_id'
            )


            ->selectRaw("

            users.name as user,

            stores.name as store,


            COUNT(
                DISTINCT DATE(attendances.created_at)
            ) as attendance,



            SUM(
                CASE

                    WHEN attendances.type = 3

                    AND TIME(attendances.created_at)
                        <= ADDTIME(
                            TIME(users.entry_time),
                            '00:05:00'
                        )

                    THEN 1

                    ELSE 0

                END
            ) as ontime,



            SUM(
                CASE

                    WHEN attendances.type = 3

                    AND TIME(attendances.created_at)
                        > ADDTIME(
                            TIME(users.entry_time),
                            '00:05:00'
                        )

                    THEN 1

                    ELSE 0

                END
            ) as late,



            SUM(
                CASE

                    WHEN attendances.type = 4

                    AND TIME(attendances.created_at)
                        <
                        SUBTIME(
                            TIME(users.exit_time),
                            '00:05:00'
                        )

                    THEN 1

                    ELSE 0

                END
            ) as early_exit,



            SUM(
                CASE

                    WHEN attendances.type = 4

                    THEN 1

                    ELSE 0

                END
            ) as exits,



            SEC_TO_TIME(

                AVG(

                    CASE

                        WHEN attendances.type = 3

                        THEN TIME_TO_SEC(
                            TIME(attendances.created_at)
                        )

                    END

                )

            ) as avg_entry,



            SEC_TO_TIME(

                AVG(

                    CASE

                        WHEN attendances.type = 'OUT'

                        THEN TIME_TO_SEC(
                            TIME(attendances.created_at)
                        )

                    END

                )

            ) as avg_exit


        ")


            ->groupBy(

                'users.id',
                'users.name',
                'stores.name',
                'users.entry_time',
                'users.exit_time'

            )


            ->paginate(10);

    }




    public function calculateKpis()
    {

        $query = Attendance::query();


        if($this->from)
            $query->whereDate(
                'created_at',
                '>=',
                $this->from
            );


        if($this->to)
            $query->whereDate(
                'created_at',
                '<=',
                $this->to
            );


        if($this->user)
            $query->where(
                'user_id',
                $this->user
            );



        $this->totalAttendance =
            $query->count();



        $this->totalUsers =
            User::when($this->store,function($q){

                $q->where(
                    'store_id',
                    $this->store
                );

            })
                ->count();



        // temporal
        $this->onTime =
            $query->where('type','IN')
                ->count();


    }




    public function render()
    {

        $this->users = User::all();

        $this->stores = Store::all();


        $this->calculateKpis();


        $chart =
            $this->getAttendanceChart();



        return view(
            'livewire.attendances-view',
            [

                'attendanceLabels'
                =>
                    $chart->pluck('name'),


                'attendanceSeries'
                =>
                    $chart
                        ->pluck('attendance')
                        ->map(fn($x)=>(int)$x),


                'attendances'
                =>
                    $this->getAttendances()

            ]
        );

    }


}
