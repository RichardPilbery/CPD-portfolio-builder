import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';

function updateWordCount() {
    console.log("Updating word count");
    var id = document.getElementsByClassName("word-count");
    if (id.length > 0) {
        var word_total_element = document.getElementById("word-total");
        var word_total = 0;
        Array.prototype.map.call(id, function(x) {
            word_total += parseInt(x.innerHTML, 10);
        })
        word_total_element.textContent = word_total.toString();
    }
}

type WordCountComponentProps = {
    text?: string,
    label?: string,
    section_name?: string,
    placeholder?: string,
    popup?: string,
    errors?: boolean,
    error_message?: string,
    include_wordcount?: boolean,
    profile?: boolean,
}


const WordCountComponent = ({text, label, section_name, popup, errors, error_message, include_wordcount, profile} : WordCountComponentProps) => {
    // console.log('Label ' + label + ' and profile status is ' + profile);
    // console.log('Word Count Component');
    // console.log('Error status: '+errors);
    const [wordCount, setWordCount] = useState(0);
    const [theText, setTheText] = useState(text);

    useEffect(() => {
        //console.log('The text has changed');
        setWordCount(countWords(text));
        if(!profile && include_wordcount) updateWordCount();
    }, []);

    const checkTheWords = (paragraph) => {
        // console.log('Words');
        setTheText(paragraph);
        setWordCount(countWords(paragraph));
        if(!profile && include_wordcount) updateWordCount();
    }

    const countWords = (s) => {
        s = s.replace(/(^\s*)|(\s*$)/gi,"");//exclude  start and end white-space
        s = s.replace(/[ ]{2,}/gi," ");//2 or more space to 1
        s = s.replace(/\n /,"\n"); // exclude newline with a start spacing
        console.log()
        const num_words = s.split(' ').filter(function(str){return str!="";}).length;
        return num_words;
    }

    return(
        <>
        <div className="mb-4">
            <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor={section_name}>
                {label}
            </label>
                <textarea onChange={(event) => checkTheWords(event.target.value)} rows="6" className={`shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline ${errors ? 'is-invalid': ''}`} id={section_name} name={section_name} value={theText}>
                </textarea>
                <div className="error-fb">
                    <sub>{errors ? error_message : ''}</sub>
                </div>
                { include_wordcount &&
                    <p className="text-right">Word count is: <span className="word-count">{wordCount}</span></p>
                }
        </div>
        </>
    );
}

export default WordCountComponent;

if (document.getElementsByClassName('word_count_component')) {
    const elements = Array.from(document.getElementsByClassName('word_count_component') as HTMLCollectionOf<HTMLElement>);
    // create new props object with element's data-attributes
    elements.forEach((element) => {
        //console.log(element);
        const props = Object.assign({}, element.dataset);
        ReactDOM.render(<WordCountComponent {...props}/>,element);
    })

}

