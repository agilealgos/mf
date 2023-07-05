import React, {Component} from 'react';
import { connect } from "react-redux";
import RowActions from './actions/RowActions';
import OffersSelector from './OffersSelector';

const {openNewColumnSelector} = RowActions;

class RightColumnOptions extends Component
{
    static mapStateToProps(state, props) {
        return props;
    };

    render() 
    {
        return (
            <div className="flex flex-col">
                <OffersSelector 
                    offers={this.props.offersData} 
                    offersSetRelation={this.props.offersSetRelation}
                    columnTemporaryId={this.props.columnTemporaryId}
                    contextTemporaryID={this.props.contextTemporaryID}
                />
                <button className="space-x-1 items-center justify-center mt-3 mb-[-8px] inline-flex bg-gray-300 text-gray-550 px-8 h-8 rounded-1" onClick={() => {
                    let open = true;

                    if ((this.props.offersData || []).length) {
                        if (!window.confirm(this.props.newColumnConfirmationMessage)) {
                            open = false;
                        }
                    }

                    if (open) {
                        this.props.openNewColumnSelector(
                            this.props.columnTemporaryId
                        );
                    }
                }}>
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clipRule="evenodd" />
                    </svg>
                    <span>add column</span>
                </button>
            </div>
        );
    }
}

export default connect(RightColumnOptions.mapStateToProps, {openNewColumnSelector})(RightColumnOptions);