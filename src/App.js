import { useState } from '@wordpress/element';
import BookList from './components/BookList';
import BookForm from './components/BookForm';


const App = () => {
    const [view, setView] = useState('list'); 
    const [currentBook, setCurrentBook] = useState(null);

    const handleEdit = (book) => {
        setCurrentBook(book);
        setView('edit') ;
   


    };

    const handleCancel = () => {
        setCurrentBook(null);
        setView('list');


    };

    return (
        <div className="wrap">
            <h1 className="wp-heading-inline">Library Manager</h1>
            {view === 'list' && (
                
                <button className="page-title-action" onClick={() => setView('add')}>Add New</button>
            )}
            <hr className="wp-header-end" />

            {view === 'list' && <BookList onEdit={handleEdit} />}
            
            {(view === 'add' || view === 'edit') && (
                <BookForm 
                    book={currentBook} 
                    onSave={() => setView('list')} 
                    onCancel={handleCancel} 

                />
            )}
        </div>
    );
};

export default App;