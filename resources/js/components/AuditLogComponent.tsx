import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';
import Echo from 'laravel-echo';
import axios from 'axios';
import moment from 'moment';

type AuditLogComponentProps = {
    audittype: string,
    userid: number,
    start: string,
    end: string,
}

const echo = new Echo(
    {
        broadcaster: "pusher",
        key: process.env.MIX_PUSHER_APP_KEY,
        wsHost: 'realtime-pusher.ably.io',
        wsPort: 443,
        disableStats: true,
        encrypted: true,
    }
);

const AuditLogComponent: React.FC<AuditLogComponentProps> = ({audittype, userid, start, end}) => {
    console.log('Audit type is ' + audittype);
    const [show, setShow] = useState(3);
    const [startDate, setStartDate] = useState(start);
    const [endDate, setEndDate] = useState(end);
    const postUrl = audittype == "audit" ? "/audit/download" : "/audit/airway";

    const sendPrintRequest = async () => {
        setShow(2);
        await axios.post(postUrl, {
            start: startDate,
            end: endDate,
        })
        .then((response) => {
            /// console.log(response);
        })
    }

    const downloadRequest = (e = null) => {
        if(e !== null) {
            e.preventDefault();
        }
        console.log('Download request triggered');
        setShow(1);
        window.open(`/audit/downloadlog/${audittype}`, "_self");
    }

    useEffect(() => {
        console.log('Listening set up');
        if(audittype == "audit") {
            console.log('Setting up auditlog listener');
            echo.private('user.auditlog.' + userid)
            .listen('AuditLogReadyForDownload',(e) => {
                console.log(e);
                downloadRequest();
            })
        } else {
            console.log('Setting up airwaylog listener');
            echo.private('user.airwaylog.' + userid)
            .listen('AirwayLogReadyForDownload',(e) => {
                downloadRequest();
            })          
        }
    }, []);

    const handleChange = (e) => {
        setShow(3);
        console.log(e.target.id);
        let start = moment(startDate);
        let end = moment(endDate);
        const currentTime = moment();

        if(e.target.id == "start" && moment(e.target.value).isValid()) {
                start = moment(e.target.value);

        } else if(e.target.id == "start" && moment(e.target.value).isValid()) {
                end = moment(e.target.value);
        }
        // Check end < start
        if(end.isAfter(start)) {
            setStartDate(start.format('YYYY-MM-DD'));
            setEndDate(end.format('YYYY-MM-DD'));
        }
    }


    return(
        <>
            <div className="pt-4">
                <form method="POST" action="/audit/download" className="ml-2 flex flex-wrap w-full">
                    <div className="pr-2">
                        <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="start">
                            Start date
                        </label>
                        <input type="date" className="h-10 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="start" name="start" placeholder="Enter start date" value={startDate} onChange={handleChange} required/>
                    </div>

                    <div className="pr-2">
                        <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="end">
                            End date
                        </label>
                        <input type="date" className="h-10 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="end" name="end" value={endDate} onChange={handleChange} required/>
                    </div>

                    <div className="pr-2">
                        <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="end">
                            &nbsp;
                        </label>
                        { show == 1 &&
                            <button className="green-btn" onClick={(e) => downloadRequest(e)}>{audittype == 'audit' ? 'Download Audit Log' : 'Download Airway Log'}</button>
}
                        {show == 3 &&
                            <button className="btn" onClick={(e) => sendPrintRequest()}>{audittype == 'audit' ? 'Download Audit Log' : 'Download Airway Log'}</button>
                        }
                        {show == 2 &&
                            <button type="button" className="inline-flex items-center px-4 py-2 border border-transparent text-base leading-6 font-light rounded-md text-white bg-purple-600 hover:bg-purple-500 focus:outline-none focus:border-purple-700 focus:shadow-outline-purple active:bg-purple-700 transition ease-in-out duration-150 cursor-not-allowed" disabled="">
                            <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                                Processing
                            </button>
                        }
                    </div>
                </form>
            </div>
        </>
    );
}
export default AuditLogComponent;


if (document.getElementById('audit_log_component')) {
    const element = document.getElementById('audit_log_component');
    // create new props object with element's data-attributes
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<AuditLogComponent {...props}/>,
        document.getElementById('audit_log_component'));
}
