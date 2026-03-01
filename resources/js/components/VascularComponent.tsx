import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';

type VascularComponentProps = {
    iv_types?: string,
    iv_sites?: string,
    sel_vascular?: string,
}

const VascularComponent: React.FC<VascularComponentProps> = ({iv_types, iv_sites, sel_vascular}) => {
    const [showForm, setShowForm] = useState(false);
    const [ivtype] = useState(JSON.parse(iv_types));
    const [ivsite] = useState(JSON.parse(iv_sites));
    const [vascularInts, setVascularInt] = useState();
    const [editVascularInts, setEditVascularInt] = useState();
    const [listOfVasculars, setListOfVasculars] = useState([]);
    const [selectedVasculars, setSelectedVasculars] = useState(JSON.parse(sel_vascular));
    const [vascularObject, setVascularObject] = useState();

    useEffect(() => {
        if(vascularInts) {
            setListOfVasculars([...listOfVasculars, vascularInts]);
            setVascularObject();
            setShowForm(false);
        }
        if(selectedVasculars && selectedVasculars.length > 0) {
            setListOfVasculars(selectedVasculars);
            setSelectedVasculars([]);
            setVascularObject();
            setShowForm(false);
        }
    }, [vascularInts, selectedVasculars]);

    useEffect(() => {
        if(typeof editVascularInts !== 'undefined') {
            setShowForm(true);
            setVascularObject(editVascularInts);
        }
    }, [editVascularInts]);

    useEffect(() => {
        try {
            const tempArray = [];
            Object.entries(JSON.parse(sel_vascular)).forEach(([key, value]) => { tempArray[key] = value});
            if(tempArray.length > 0) {
                console.log(tempArray);
                setSelectedVasculars(tempArray);
            }

        } catch {
            console.log('Did not work');
        }
    }, [sel_vascular]);

    return(
        <div className="mb-4 pt-3">
            {!showForm && <button className="shadow appearance-none bg-purple-600 hover:bg-purple-700 border-purple-600 hover:border-red-700 leading-tight border text-white py-2 px-3 rounded lg:w-4/12" type="button" onClick={() => setShowForm(!showForm)}>Add Vascular Intervention</button>}
            {showForm &&
            <>
                <VascularFormComponent ivtype={ivtype} ivsite={ivsite} setvasculars={setVascularInt} vascularObject={vascularObject} />
            </>
            }
            {listOfVasculars &&
                <div id="vascularskills" className="pt-4">
                    <VascularListComponent listOfVasculars={listOfVasculars} ivtype={ivtype} ivsite={ivsite} seteditvasculars={setEditVascularInt} />
                </div>
            }
        </div>
    );
}
export default VascularComponent;

const VascularFormComponent: React.FC<any> = ({ivtype, ivsite, setvasculars, vascularObject}) => {
    console.log('Add/Edit vascular');
    const [ivTypeSelected] = useState(typeof vascularObject !== 'undefined' ? vascularObject.ivtype_id : null);
    console.log('IV selected is ' + ivTypeSelected);
    const [ivSiteSelected] = useState(typeof vascularObject !== 'undefined' ? vascularObject.ivsite_id : null);
    const [ivlocationSelected] = useState(typeof vascularObject !== 'undefined' ? vascularObject.location : 0);

    const processForm = (event: any) => {
        event.preventDefault();
        let vascularObject = {
            "ivtype_id": event.target.ivtype_id.value,
            "success": event.target.success.checked ? 1 : 0,
            "size": event.target.size.value,
            "location": event.target.location.value,
            "ivsite_id": event.target.ivsite_id.value,
        };
        setvasculars(vascularObject);
    }

    return (
        <>
        <div className="m-4 p-4 border-2 border-purple-700 rounded">
            <form onSubmit={processForm}>
                <div className="mb-4">
                    <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="ivtype_id">Vascular intervention</label>
                    <select className="block shadow border rounded text-lg" name="ivtype_id" id="ivtype_id" defaultValue={ivTypeSelected}>
                        {ivtype && Object.entries(ivtype).map(([key, value]) => {
                            return <option key={key} value={key}>{value}</option>
                        })}
                    </select>
                </div>
                <div className="mb-4">
                    <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="success">Successful attempt</label>
                    <input className="block shadow border rounded text-lg" type="checkbox" name="success" id="success" defaultChecked={typeof vascularObject != 'undefined' && vascularObject.success == 1 ? 'checked' : '' } />
                </div>

                <div className="mb-4">
                    <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="size">Size</label>
                    <input className="block shadow border rounded text-lg w-20" type="number" name="size" id="size" min={0} max={50} step={1} empty="-- Choose --" defaultValue={ typeof vascularObject != 'undefined' ? vascularObject.size : null }/>
                </div>

                <div className="mb-4">
                    <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="location">Location (relative to patient)</label>
                    <select className="block shadow border rounded text-lg" name="location" id="location" defaultValue={ivlocationSelected}>
                        <option value="0">-- Choose --</option>
                        <option value="left">Left</option>
                        <option value="right">Right</option>
                    </select>
                </div>

                <div className="mb-4">
                    <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="ivsite_id">Insertion site</label>
                    <select className="block shadow border rounded text-lg" name="ivsite_id" id="ivsite_id" defaultValue={ivSiteSelected}>
                        <option value="0">-- Choose --</option>
                        {ivsite && Object.entries(ivsite).map(([key, value]) => {
                            return <option key={key} value={key}>{value}</option>
                        })}
                    </select>
                </div>

                <button className="mt-4 shadow appearance-none bg-purple-600 hover:bg-purple-700 border-purple-600 hover:border-red-700 leading-tight border text-white py-2 px-3 rounded lg:w-3/12" type="submit">
                    {typeof vascularObject != 'undefined' ? 'Update' : 'Submit'}
                </button>
            </form>
        </div>
        </>
    )
};

const VascularListComponent: React.FC<any> = ({listOfVasculars, ivtype, ivsite, seteditvasculars}) => {
    let tempArray = [];
    let iterator = 0;
    // Note addition of || "" which avoids error about null input values
    listOfVasculars.forEach((row, index) => {
        tempArray.push(
            <div className="pt-2 flex" key={index} id={`vascular-${iterator}`}>
                <input id={`vascular[${iterator}][display]`}  name={`vascular[${iterator}][display]`} className={`shadow appearance-none border rounded w-8/12 py-2 px-3 text-gray-700 leading-tight bg-gray-200 ${row.success ? "border-green-700": "border-red-700"} mr-6`} type="text" value={`${ivtype[row.ivtype_id].valueOf()} ${row.location != 0 ? row.location : ''} ${(ivsite[row.ivsite_id]? ivsite[row.ivsite_id] : '').toLowerCase()}`} readOnly />
                <input id={`vascular[${iterator}][size]`}  name={`vascular[${iterator}][size]`} type="hidden" value={row.size} />
                <input id={`vascular[${iterator}][success]`}  name={`vascular[${iterator}][success]`} type="hidden" value={row.success} />
                <input id={`vascular[${iterator}][ivtype_id]`}  name={`vascular[${iterator}][ivtype_id]`} type="hidden" value={row.ivtype_id} />
                <input id={`vascular[${iterator}][location]`}  name={`vascular[${iterator}][location]`} type="hidden" value={row.location} />
                <input id={`vascular[${iterator}][ivsite_id]`}  name={`vascular[${iterator}][ivsite_id]`} type="hidden" value={row.ivsite_id} />
                <button className="shadow appearance-none bg-purple-600 hover:bg-purple-700 border-purple-600 hover:border-purple-700 leading-tight border text-white py-2 px-3 mr-4 rounded w-2/12" type="button" onClick={event => { seteditvasculars(row); event.target.parentNode.remove();}} >
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


if (document.getElementById('vascular_component')) {
    const element = document.getElementById('vascular_component');
    // create new props object with element's data-attributes
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<VascularComponent {...props}/>,
        document.getElementById('vascular_component'));
}
