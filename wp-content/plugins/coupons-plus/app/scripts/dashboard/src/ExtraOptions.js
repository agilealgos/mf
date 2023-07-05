import React, {Component} from 'react';
import AutoApplyOptions from './options/AutoApplyOptions';

export default class ExtraOptions extends Component
{
    render() 
    {
        return (
            <div className="flex flex-col space-y-5 py-6 px-4 mt-14 mx-[-16px] mb-[-16px] bg-gray-250 bg-opacity-20 border-t-[2px] border-gray-300">
                <h1 className="text-1x text-gray-500">{__('Tools', CouponsPlus.textDomain)}</h1>
                <div className="flex max-w-[218]">
                    <AutoApplyOptions />
                </div>
            </div>
        )
    }
}