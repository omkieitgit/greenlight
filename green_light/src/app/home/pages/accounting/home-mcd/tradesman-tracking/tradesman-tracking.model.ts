export class TradesmanTrackingModel{
        id:number;
        name:string;
        working_date:string;
        arrival_time:string|number;
        morning_time_in:string;
        morning_time_out:string;
        afternoon_time_in:string;
        afternoon_time_out:string;
        work_hours:string;
        total_work_hours:string;
        description:string;
        remarks:string;
}


export const TimesObj={
        '00:00:00':'12:00 AM',
        '01:00:00':'1:00 AM',
        '02:00:00':'2:00 AM',
        '03:00:00':'3:00 AM',
        '04:00:00':'4:00 AM',
        '05:00:00':'5:00 AM',
        '06:00:00':'6:00 AM',
        '07:00:00':'7:00 AM',
        '08:00:00':'8:00 AM',
        '09:00:00':'9:00 AM',
        '10:00:00':'10:00 AM',
        '11:00:00':'11:00 AM',
        '12:00:00':'12:00 PM',
        '13:00:00':'1:00 PM',
        '14:00:00':'2:00 PM',
        '15:00:00':'3:00 PM',
        '16:00:00':'4:00 PM',
        '17:00:00':'5:00 PM',
        '18:00:00':'6:00 PM',
        '19:00:00':'7:00 PM',
        '20:00:00':'8:00 PM',
        '21:00:00':'9:00 PM',
        '22:00:00':'10:00 PM',
        '23:00:00':'11:00 PM',
}