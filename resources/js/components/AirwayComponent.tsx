import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';

type AirwayComponentProps = {
    airway_types?: string,
    cap_types?: string,
    sel_airway?: string,
}

const AirwayComponent: React.FC<AirwayComponentProps> = ({airway_types, cap_types, sel_airway}) => {
    console.log('Airway Component');
    const [showForm, setShowForm] = useState(false);
    const [airwaytype] = useState(JSON.parse(airway_types));
    const [captype] = useState(JSON.parse(cap_types));
    const [airwayInts, setAirwayInt] = useState();
    const [editAirwayInts, setEditAirwayInt] = useState();
    const [listOfAirways, setListOfAirways] = useState([]);
    const [selectedAirways, setSelectedAirways] = useState(JSON.parse(sel_airway));
    const [airwayObject, setAirwayObject] = useState();

    useEffect(() => {
        if(airwayInts) {
            setListOfAirways([...listOfAirways, airwayInts]);
            setAirwayObject();
            setShowForm(false);
        }
        if(selectedAirways && selectedAirways.length > 0) {
            setListOfAirways(selectedAirways);
            setSelectedAirways([]);
            setAirwayObject();
            setShowForm(false);
        }
    }, [airwayInts, selectedAirways]);

    useEffect(() => {
        if(typeof editAirwayInts !== 'undefined') {
            setShowForm(true);
            setAirwayObject(editAirwayInts);
        }
    }, [editAirwayInts]);

    useEffect(() => {
        try {
            const tempArray = [];
            Object.entries(JSON.parse(sel_airway)).forEach(([key, value]) => { tempArray[key] = value});
            if(tempArray.length > 0) {
                console.log('Temp Array');
                console.log(tempArray);
                setSelectedAirways(tempArray);
            }

        } catch {
            console.log('Did not work');
        }
    }, [sel_airway]);

    return(
        <div className="mb-4 pt-3">
            {!showForm && <button className="shadow appearance-none bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-red-700 leading-tight border text-white py-2 px-3 rounded lg:w-4/12" type="button" onClick={() => setShowForm(!showForm)}>Add Airway Skill/Device</button>}
            {showForm &&
            <>
                <FormComponent airwaytype={airwaytype} captype={captype} setairways={setAirwayInt} airwayObject={airwayObject} />
            </>
            }
            {listOfAirways &&
                <div id="airwayskills" className="pt-4">
                    <AirwayListComponent listOfAirways={listOfAirways} airwaytype={airwaytype} seteditairways={setEditAirwayInt} />
                </div>
            }
        </div>
    );
}
export default AirwayComponent;

const FormComponent: React.FC<any> = ({airwaytype, captype, setairways, airwayObject}) => {
    console.log(airwayObject);
    const [airwaySelected] = useState(typeof airwayObject !== 'undefined' ? airwayObject.airwaytype_id : null);
    const [capnographySelected] = useState(typeof airwayObject !== 'undefined' ? airwayObject.capnography_id : null);

    const processForm = (event: any) => {
        event.preventDefault();
        let airwayObject = {
            "airwaytype_id": event.target.airwaytype_id.value,
            "success": event.target.success.checked ? 1 : 0,
            "grade": event.target.grade.value,
            "size": event.target.size.value,
            "bougie": event.target.bougie.checked ? 1 : 0,
            "capnography_id": event.target.capnography_id.value,
            "notes": event.target.notes.value
        };
        setairways(airwayObject);
    }

    return (
        <>
        <div className="m-4 p-4 border-2 border-blue-700 rounded">
            <form onSubmit={processForm}>
                <div className="mb-4">
                    <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="airwaytype_id">Airway device/skill</label>
                    <select className="block shadow border rounded text-lg" name="airwaytype_id" id="airwaytype_id" defaultValue={airwaySelected} >
                        {airwaytype && Object.entries(airwaytype).map(([key, value]) => {
                            return <option key={key} value={key} >{value}</option>
                        })}
                    </select>
                </div>
                <div className="mb-4">
                    <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="success">Successful attempt</label>
                    <input className="block shadow border rounded text-lg" type="checkbox" name="success" id="success" defaultChecked={typeof airwayObject != 'undefined' && airwayObject.success == 1 ? 'checked' : '' } />
                </div>

                <div className="mb-4">
                    <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="grade">Cormack-Lehane Grade (intubation)</label>
                    <input className="block shadow border rounded text-lg w-20" type="number" name="grade" id="grade" min={1} max={4} defaultValue={ typeof airwayObject != 'undefined' ? airwayObject.grade : null } />
                </div>

                <div className="mb-4">
                    <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="size">Size of device</label>
                    <input className="block shadow border rounded text-lg w-20" type="number" name="size" id="size" min={0.5} max={10} step={0.5} defaultValue={ typeof airwayObject != 'undefined' ? airwayObject.size : null }/>
                </div>

                <div className="mb-4">
                    <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="bougie">Bougie used</label>
                    <input className="block shadow border rounded text-lg" type="checkbox" name="bougie" id="bougie" defaultChecked={typeof airwayObject != 'undefined' && airwayObject.bougie == 1 ? 'checked' : '' } />
                </div>

                <div className="mb-4">
                    <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="capnography_id">Capnography type</label>
                    <select className="block shadow border rounded text-lg" name="capnography_id" id="capnography_id" defaultValue={capnographySelected}>
                        <option value="0">-- Choose --</option>
                        {captype && Object.entries(captype).map(([key, value]) => {
                            return <option key={key} value={key}>{value}</option>
                        })}
                    </select>
                </div>

                <div className="mb-4">
                    <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="notes">Airway notes</label>
                    <textarea rows="4" id="notes" name="notes" className="shadow appearance-none border rounded w-10/12 py-2 px-3 leading-tight mr-6" type="search"  placeholder="Enter airway-related notes here" defaultValue={ typeof airwayObject != 'undefined' ? airwayObject.notes : null } />
                </div>

                <button className="mt-4 shadow appearance-none bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-red-700 leading-tight border text-white py-2 px-3 rounded lg:w-3/12" type="submit">
                        {typeof airwayObject != 'undefined' ? 'Update' : 'Submit'} Airway
                </button>
            </form>
        </div>
        </>
    )
};

const AirwayListComponent: React.FC<any> = ({listOfAirways, airwaytype, seteditairways}) => {
    let tempArray = [];

    let iterator = 0;
    // Note addition of || "" which avoids error about null input values
    listOfAirways.forEach((row, index) => {
        tempArray.push(
            <div className="pt-2 flex" key={index} id={`airway-${iterator}`}>
                <input id={`airway[${iterator}][display]`}  name={`airway[${iterator}][display]`} className={`shadow appearance-none border rounded w-8/12 py-2 px-3 text-gray-700 leading-tight bg-gray-200 ${row.success ? "border-green-700": "border-red-700"} mr-6`} type="text" value={airwaytype[row.airwaytype_id].valueOf()} readOnly />
                <input id={`airway[${iterator}][airwaytype_id]`}  name={`airway[${iterator}][airwaytype_id]`} type="hidden" value={row.airwaytype_id} />
                <input id={`airway[${iterator}][success]`}  name={`airway[${iterator}][success]`} type="hidden" value={row.success} />
                <input id={`airway[${iterator}][grade]`}  name={`airway[${iterator}][grade]`} type="hidden" value={row.grade || ""} />
                <input id={`airway[${iterator}][size]`}  name={`airway[${iterator}][size]`} type="hidden" value={row.size || ""} />
                <input id={`airway[${iterator}][bougie]`}  name={`airway[${iterator}][bougie]`} type="hidden" value={row.bougie} />
                <input id={`airway[${iterator}][capnography_id]`}  name={`airway[${iterator}][capnography_id]`} type="hidden" value={row.capnography_id} />
                <input id={`airway[${iterator}][notes]`}  name={`airway[${iterator}][notes]`} type="hidden" value={row.notes || ""} />
                <button className="shadow appearance-none bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-blue-700 leading-tight border text-white py-2 px-3 mr-4 rounded w-2/12" type="button" onClick={event => { seteditairways(row); event.target.parentNode.remove();}} >
                    Edit
                </button>
                <button className="shadow appearance-none bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700 leading-tight border text-white py-2 px-3 rounded w-2/12" type="button" onClick={event => event.target.parentNode.remove()}>
                    Remove
                </button>
            </div>
        );
        iterator++;
    });

    return(tempArray);

}


if (document.getElementById('airway_component')) {
    const element = document.getElementById('airway_component');
    // create new props object with element's data-attributes
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<AirwayComponent {...props}/>,
        document.getElementById('airway_component'));
}
