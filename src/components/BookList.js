import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

const BookList = ({ onEdit }) => {
    const [books, setBooks] = useState([]);
    const [isLoading, setIsLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState(''); // New: Memory for search text

    // Initial load
    useEffect(() => {
        fetchBooks();
    }, []);

    // New: Listen for typing (Debounce logic)
    useEffect(() => {
        const delayDebounce = setTimeout(() => {
            fetchBooks();
        }, 500); // Wait 500ms after user stops typing

        return () => clearTimeout(delayDebounce);
    }, [searchTerm]);

    const fetchBooks = () => {
        // Updated: Add search parameter to the URL
        const path = searchTerm 
            ? `/library/v1/books?search=${searchTerm}` 
            : '/library/v1/books';

        apiFetch({ path: path })
            .then(data => {
                setBooks(data);
                setIsLoading(false);
            })
            .catch(error => console.error(error));
    };

    const deleteBook = (id) => {
        if (!confirm('Are you sure you want to delete this book?')) return;

        apiFetch({ 
            path: `/library/v1/books/${id}`,
            method: 'DELETE'
        }).then(() => {
            fetchBooks(); // Refresh list
        });
    };

    return (
        <div>
            {/* New: Search Bar Area */}
            <div className="tablenav top">
                <div className="alignleft actions">
                    <input 
                        type="search" 
                        placeholder="Search by title or author..." 
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        style={{ height: '32px', margin: '0 10px 10px 0', minWidth: '250px' }}
                    />
                    {searchTerm && (
                        <button 
                            className="button" 
                            onClick={() => setSearchTerm('')}
                        >
                            Clear
                        </button>
                    )}
                </div>
            </div>

            {/* Table Area */}
            {isLoading ? (
                <p>Loading books...</p>
            ) : (
                <table className="library-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Year</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {books.length === 0 ? (
                            <tr><td colSpan="5">No books found.</td></tr>
                        ) : (
                            books.map(book => (
                                <tr key={book.id}>
                                    <td>{book.title}</td>
                                    <td>{book.author}</td>
                                    <td>{book.publication_year}</td>
                                    <td>
                                        {/* Uses the CSS class from Bonus 1 if you added it */}
                                        <span className={`status-${book.status}`}>{book.status}</span>
                                    </td>
                                    <td>
                                        <button className="button button-small" onClick={() => onEdit(book)}>Edit</button>
                                        <button 
                                            className="button button-small button-link-delete" 
                                            onClick={() => deleteBook(book.id)} 
                                            style={{marginLeft: '10px', color: '#a00'}}
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            )}
        </div>
    );
};

export default BookList;