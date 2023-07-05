import { Tab } from '@headlessui/react';
import React, {Component} from 'react';
import { connect } from "react-redux";
import CardIcon from './CardIcon';
import RowsActions from './actions/RowsActions';
import createCard from './conditionsOrFilters/CardCreator';
import parse from 'html-react-parser';

const {setRows} = RowsActions;

class PresetsPanel extends Component
{
    static label = __('presets', CouponsPlus.textDomain);

    static mapStateToProps(state, props) {
        return props;
    };

    static getIcon() {
        return (
            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        );
    }

    render() 
    {
        return (
            <div className="flex flex-col space-y-10 max-w-[980px]">
                {Object.keys(CouponsPlus.presets).map(category => (
                    <div className="w-full flex flex-col space-y-2">
                        <h1 className="ml-4 font-medium text-gray-600 text-2x">{category}</h1>
                        <div className="grid gap-4 grid-cols-3">
                            {CouponsPlus.presets[category].map(preset => (
                                <div key={preset.name} className="flex flex-col p-4 rounded-2 bg-gray-100 space-y-5">
                                    <div className="flex flex-col space-y-3">
                                        <p className="text-2x text-gray-650">{preset.name}</p>
                                        <div className="flex flex-col">
                                            <span className="text-smaller-1 text-gray-500">{__('Example:', CouponsPlus.textDomain)}</span>
                                            <p className="text-base text-gray-500">{this.getExample(preset.example)}</p>
                                        </div>
                                    </div>
                                    <div className="flex flex-row justify-between items-center">
                                        <div className="flex flex-row space-x-1">
                                            {Object.keys(preset.uses).map(typeOfComponent => (
                                                preset.uses[typeOfComponent].map(componentTYPE => {
                                                    const card = createCard(typeOfComponent, {type: componentTYPE})

                                                    return (<CardIcon key={componentTYPE} icon={card.getIcon()} dark={false} />)
                                                })
                                            ))}
                                        </div>
                                        <button 
                                            className="h-9 px-6 rounded-4 bg-gray-150" 
                                            onClick={this.attemptToSetRows.bind(this, preset)}
                                        >{__('Use preset', CouponsPlus.textDomain)}</button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                ))}
            </div>  
        );
    }

    getExample(example) 
    {
        example = example || '';

        return parse(example.replace('(\\n)', '<br><br>')
                      .replace(/url\((.*)\)/gi, (match, first) => {
                        const [text, url] = first.split("|");
                        return `<a href="${url.trim()}" target="_blank">${text.trim()}</a>`;
                      }))
    }

    attemptToSetRows(preset) 
    {
        if (window.confirm(__('By importing this preset, your current settings will be overriden. Are you sure want to continue?', CouponsPlus.textDomain))) {
            this.props.setRows(
                JSON.parse(preset.rows),
                'from-preset'
            )
        }
    }
}

export default connect(PresetsPanel.mapStateToProps, {setRows})(PresetsPanel);