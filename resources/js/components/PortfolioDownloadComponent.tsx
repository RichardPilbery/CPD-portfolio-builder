import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';
import Echo from 'laravel-echo';
import axios from 'axios';

type PortfolioComponentProps = {
    id: number,
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

const PortfolioDownloadComponent: React.FC<PortfolioComponentProps> = ({id}) => {
    const [show, setShow] = useState(3);

    const sendPrintRequest = async () => {
        setShow(2);
        await axios.get(`/portfolio/${id}/print`)
            .then((response) => {
                /// console.log(response);
            })
    }

    const downloadRequest = () => {
        setShow(1);
        window.open(`/portfolio/${id}/download`, "_self");
    }

    useEffect(() => {
        console.log('Listening set up');
        echo.private('portfolios.' + id)
            .listen('PortfolioEntryReadyForDownload',(e) => {
                // console.log(e);
                // setShow(1);
                downloadRequest();
    })}, []);

    // useEffect(() => {
    //     console.log('Show is ' + show);
    // }, [show]);

    return(
        <>
            { show == 1 &&
                <a className="btn green-btn" href={`/portfolio/${id}/download`}>Download</a>
            }
            {show == 3 &&
                <a className="btn" href="#" onClick={(e) => sendPrintRequest()}>Download</a>
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
        </>
    );
}
export default PortfolioDownloadComponent;


if (document.getElementById('portfolio_download_component')) {
    const element = document.getElementById('portfolio_download_component');
    // create new props object with element's data-attributes
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<PortfolioDownloadComponent {...props}/>,
        document.getElementById('portfolio_download_component'));
}
