import classNames from 'classnames';
import React, {Component} from 'react';
import { connect } from "react-redux";
import FieldsActions from './actions/FieldsActions';

class RepeaterFields extends Component
{
    state = {
        showPlaceholder: false
    }

    isNotTheLastItem(items, index) 
    {
        return items.length !== (index + 1);
    }

    render() 
    {
        let isPlaceholder;
        const items = this.props.getItems();

        return (
            <div className={classNames({
                'w-full flex flex-col space-y-1 items-start': true
            })}>
                {items.map((item, index) => (
                    <>
                        <div className="flex flex-row items-center justify-center">
                            {this.props.forEachItem(item, index, isPlaceholder = false).render()}
                            <button onClick={() => this.props.removeRepeaterItem(index, this.props.temporaryID, this.props.propertyName)} className="rounded-full bg-gray-100 text-gray-250 hover:text-gray-300 h-6 w-6 flex items-center justify-center cursor-pointer ml-1">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        {this.props.betweenItems && this.isNotTheLastItem(items, index)? this.props.betweenItems() : ''}
                    </>
                ))}
                {items.length === 0 || this.state.showPlaceholder? this.props.forEachItem(this.props.defaultItem, -1, isPlaceholder = true).render() : ''}
                {items.length > 0 && !this.state.showPlaceholder? (
                    <button 
                        className="w-full space-x-1 items-center justify-center mt-2 flex flex-row py-1 text-gray-300"
                        onClick={() => this.setState({showPlaceholder: true})}
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                              <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clipRule="evenodd" />
                            </svg>
                        <span>{__('Add Product', CouponsPlus.textDomain)}</span>
                    </button>
                ) : ''}
            </div>
        )
    }
}

export default connect(RepeaterFields.mapStateToProps, FieldsActions)(RepeaterFields);