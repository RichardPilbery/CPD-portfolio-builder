import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';

type FlashComponentProps = {
    types: string,
    message: string,
}

const FlashComponent: React.FC<FlashComponentProps> = ({type, message}) => {
    console.log('Flash Component');
    const [show, setShow] = useState(true);

    setTimeout(() => {
        setShow(false);
    }, 5000);

    return(
        <>
            { show &&
                <div className={`flex ${type == 'success' ? 'bg-green-200 text-green-900' : 'bg-red-200 text-red-900'} justify-between items-center w-full rounded-lg shadow-md p-4 my-4 pr-10`}>
                    {message}
                </div>
            }
        </>
    );
}
export default FlashComponent;


if (document.getElementById('flash_component')) {
    const element = document.getElementById('flash_component');
    // create new props object with element's data-attributes
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<FlashComponent {...props}/>,
        document.getElementById('flash_component'));
}
